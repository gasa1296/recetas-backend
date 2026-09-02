# Plan de Implementación — Nuevas Características

Sistema contexto: Laravel 13, Filament v5 (panel admin solo `admin`), API Sanctum (`medic`), SQLite dev.
Esquema actual: `medicaments` (sin marcas/labs), pivot `medicament_prescriptions` con `recommended_brand`, `files` polimórfico, roles `admin`/`medic`.

---

## Resumen ejecutivo

| # | Característica | Complejidad | Dependencias nuevas |
|---|---|---|---|
| 1 | Citas + recordatorios | Alta | `laravel-notification-channels/whatsapp` (opcional), scheduler |
| 2 | Fotos/videos paciente | Media | Storage + `File` polimórfico (ya existe) |
| 3 | Exámenes img/pdf | Media | Ídem + MIME validation |
| 4 | Estadísticas prescripción | Alta | Tablas denormalizadas + consultas agregadas |

**Bloqueante transversal**: el sistema no tiene **auth de paciente** (solo `medic`), ni **marcas/laboratorios** en `medicaments`. Sin resolver estos, `#1` y `#4` no se pueden completar como se describen.

---

## Característica 1 — Citas y recordatorios

### 1.1 Modelo de datos

Migración `create_appointments_table`:

```php
Schema::create('appointments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // médico
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
    $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('specialty_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamp('starts_at');
    $table->timestamp('ends_at')->nullable();
    $table->string('reason')->nullable();
    $table->string('status')->default('scheduled'); // scheduled|confirmed|cancelled|completed|no_show
    $table->text('notes')->nullable();
    // recordatorios
    $table->string('reminder_channel')->default('email'); // email|whatsapp|sms
    $table->timestamp('reminder_sent_at')->nullable();
    $table->boolean('reminder_enabled')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['user_id', 'starts_at']);
    $table->index(['patient_id', 'starts_at']);
    $table->index(['status', 'starts_at']);
});
```

Modelo `Appointment` con relaciones `user()`, `patient()`, `room()`, `specialty()`. Enum `AppointmentStatus`.

### 1.2 Verificación de disponibilidad (overlap)

Un médico no puede tener 2 citas solapadas. Validación en `AppointmentRequest` + servicio:

```php
// AppointmentService::hasConflict($userId, $startsAt, $endsAt, $ignoreId)
return Appointment::where('user_id', $userId)
    ->where('status', '!=', 'cancelled')
    ->where('id', '!=', $ignoreId)
    ->where('starts_at', '<', $endsAt)
    ->where(function ($q) use ($startsAt) { $q->where('ends_at', '>', $startsAt); })
    ->exists();
```

Índice compuesto `(user_id, starts_at)` habilita el rango.

### 1.3 API (médico, `auth:sanctum`)

`AppointmentController` dentro del grupo `medicRoutes()`:

```
GET    /appointments                    → listar (?from & ?to & ?status)
POST   /appointments                    → crear
GET    /appointments/{appointment}      → detalle
PUT    /appointments/{appointment}      → reprogramar/cambiar estado
DELETE /appointments/{appointment}      → cancelar
```

Status changes en rutas verbales: `POST /appointments/{id}/confirm`, `/cancel`, `/complete`.

### 1.4 Filament

- En el panel solo entra `admin`. Agregar resource `Appointments` en Filament para gestionar/ver.
- Para que `medic` gestione sus citas → abrir panel a `medic` (decisión de alcance, preguntar).

### 1.5 Recordatorios — scheduler + job (patrón certificados)

`AppointmentReminderJob` cada 30 min (`Schedule::job(...)->everyThirtyMinutes()->withoutOverlapping()`):
1. Busca citas `scheduled|confirmed` que empiecen en `[now, now+24h]` con `reminder_sent_at IS NULL` y `reminder_enabled=1`.
2. `chunkById(50)` para evitar falta de memoria.
3. Envía notificación según `reminder_channel`.
4. Marca `reminder_sent_at` (idempotente, evita doble envío).

**Canal de envío**: el proyecto usa `MAIL_MAILER=log` (dev). Para correo real: `AppointmentReminderNotification` (Laravel Notification) + configurar mail. Para WhatsApp/SMS real: `laravel-notification-channels/whatsapp` o driver SMS (Twilio/Vonage) — requiere credenciales y **número de teléfono del paciente con código país**.

### 1.6 Correcciones previas al recordatorio

- Asegurar `patients.phone` en E.164 (`+58...`) o agregar `phone_country_code`.
- Confirmar semántica de `patients.email` (¿es del paciente? si es del consultorio, el envío falla en la vida real).

### 1.7 Tests

`tests/Feature/AppointmentControllerTest.php` + `tests/Feature/AppointmentReminderJobTest.php`:
- Crear, overlap rechazado (422), reprogramar, cancelar.
- Job: cita en 24h sin reminder → `reminder_sent_at` seteado; cita lejana → no toca; `Notification::fake`.

---

## Característica 2 — Fotos y videos del paciente

### 2.1 Infraestructura (reutilizar `File` polimórfico)

`File` ya es polimórfico (`model_type`/`model_id`). Agregar metadatos:

```php
// migración add_metadata_to_files
$table->string('mime_type')->nullable();   // image/jpeg, video/mp4
$table->unsignedBigInteger('size')->nullable();
$table->string('title')->nullable();
$table->json('meta')->nullable();          // duración video, dimensión, tag
```

Relación en `Patient`:
```php
public function media(): MorphMany { return $this->morphMany(File::class, 'model'); }
```

### 2.2 Storage y disk

- Disco dedicado `local` o `public`. Hoy `Prescription::handleUploadFile()` usa `disk('local')` pero `File::url` apunta a `storage/...` (público). Decidir según política (privado con descarga firmada vs intranet pública).
- **Videos** >10MB: subir por chunks (client-side); Laravel no lo maneja nativo — nota de diseño.

### 2.3 Validación MIME

```php
'file' => ['required', 'file',
    'mimes:jpg,jpeg,png,webp,heic,mp4,mov,mkv',
    'max:51200'] // 50MB
```

HEIC sin vista previa nativa → convertir a JPEG (`intervention/image` + `imagick`). Opcional.

### 2.4 API

`PatientMediaController` (anidado bajo paciente):

```
POST   /patients/{patient}/media           → upload foto/video (+ title, tags)
GET    /patients/{patient}/media           → listar
GET    /patients/{patient}/media/{file}    → servir/descargar
DELETE /patients/{patient}/media/{file}    → eliminar
```

Policy: solo `medic` autor del paciente o `admin`.

### 2.5 Filament

- Relation manager "Media" en `PatientResource` con `FileUpload` multiple-accept, preview (imagen → thumbnail, video → ícono + duración).
- **Privacidad**: fotos RX/lesiones = dato de salud (HIS). Acceso autenticado, nunca público. Cifrado at-rest si se sube a nube.

### 2.6 Tests

- Upload imagen/video válido → 201 y archivo en disco.
- MIME inválido (.exe) → 422.
- `meta` correcto (photo/video).
- Autorización: medic sin acceso al paciente → 403.

---

## Característica 3 — Exámenes (imágenes o PDF)

### 3.1 Modelo

Nueva entidad `Examination` (noción clínica, no solo archivo):

```php
Schema::create('examinations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
    $table->string('name');            // "Hemograma", "RX tórax"
    $table->date('examined_at')->nullable();
    $table->text('result')->nullable();
    $table->string('lab_name')->nullable();
    $table->string('status')->default('available'); // available|expired
    $table->timestamps();
    $table->softDeletes();
    $table->index(['patient_id', 'examined_at']);
});
```

Adjuntos → `morphMany(File::class, 'model')`. Un examen puede tener varios.

### 3.2 Almacenamiento

- PDF/imágenes en disco `local` o `public` (mismo criterio que Fotos).
- Validación `mimes:jpg,jpeg,png,webp,pdf,heic` + `max`.

### 3.3 API

`ExaminationController` (anidado paciente):

```
GET    /patients/{patient}/examinations
POST   /patients/{patient}/examinations                       → crear con adjunto
POST   /patients/{patient}/examinations/{examination}/files   → subir adjunto
GET    /patients/{patient}/examinations/{examination}
DELETE /patients/{patient}/examinations/{examination}
```

### 3.4 Filament

- `Examinations` resource + RelationManager en `PatientResource`.
- Preview PDF (`<iframe>`/preview) e imágenes.

### 3.5 Tests

- Crear examen con PDF → 201, archivo guardado, MIME validado.
- Ligar a prescripción.
- Múltiples adjuntos a un examen.

---

## Característica 4 — Estadísticas de prescripción

### 4.1 Modelo: marcas y laboratorios (catálogo de sugerencias)

`medicaments` **no tiene marca ni laboratorio**. La tabla `brands` es **solo catálogo de sugerencias**: se usa para autocompletar la marca al prescribir, pero **no es FK enlazada** a la prescripción. `recommended_brand` (string) conserva el valor elegido libremente por el médico.

```php
Schema::create('laboratories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('country')->nullable();
    $table->timestamps();
});

Schema::create('brands', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->foreignId('laboratory_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

Mapeo medicamento→marcas sugeridas: pivot `brand_medicament` (muchos-a-muchos; un principio activo lo fabrican varias marcas). Solo informativo para el select de sugerencias.

### 4.2 Pivot de prescripción (sin cambios)

**`medicament_prescriptions.recommended_brand` se mantiene como string.** No se migra a FK. Guarda la marca que el médico efectivamente recomendó (puede ser libre o seleccionada de las sugerencias de `brands`).

### 4.3 Datos de laboratorio en etiquetas

Validar qué atributos tiene el catálogo. Hoy solo `active_ingredient/type/group/concentration`. Falta **nombre comercial (marca)** y **laboratorio**. Necesita carga de datos (seed) o CRUD Filament.

### 4.4 Capa de estadísticas — read model

Tabla agregada `medication_statistics`:

```php
Schema::create('medication_statistics', function (Blueprint $table) {
    $table->id();
    $table->date('day');
    $table->foreignId('medicament_id')->constrained()->cascadeOnDelete();
    $table->unsignedInteger('prescription_count')->default(0);
    $table->unsignedInteger('quantity')->default(0);      // sum medicament_quantity
    $table->unsignedInteger('total_days')->default(0);    // sum duration
    $table->timestamps();
    $table->unique(['day', 'medicament_id']);
});
```

Actualización: **event listener** en `MedicamentPrescription::saved/deleted` (acumula por día) + **job nocturno de reconciliación** (evita drift).

### 4.5 Dimensiones en consultas

- **Por medicamento**: `GROUP BY medicament_id`.
- **Por paciente**: join pivot → `GROUP BY patient_id`.
- **Por marca recomendada**: `GROUP BY recommended_brand` (string del pivot de prescripción). Si se quiere vincular a laboratorio, cruzar el string contra `brands.name` para resolver el `laboratory_id`.
- **Por laboratorio**: cruzar `recommended_brand` con `brands.name` → `GROUP BY laboratory_id`.
- **Por médico**: `GROUP BY user_id`.
- Filtro por `status IN (1, 3)` (active, fully_dispensed) y rango de fechas.

> Nota: como `recommended_brand` es string libre, las estadísticas por marca/laboratorio dependen de consistencia en el texto (normalizar/trim al guardar, o resolver contra `brands` al cruzar).

### 4.6 API de reportes (solo admin)

`StatisticsController` bajo `auth:sanctum` + `middleware('permission:statistics.view')`:

```
GET  /statistics/prescriptions?group_by=medicament|patient|brand|laboratory|doctor&from=&to=
GET  /statistics/prescriptions/summary?from=&to=
GET  /statistics/prescriptions/top?group_by=medicament&limit=10
```

### 4.7 Filament — dashboards

- `StatsOverview` widgets: total recetas/mes, top medicamentos, top marcas, top laboratorios.
- `ChartWidget` (Chart.js con Filament) por rango de fechas.
- Permiso `statistics.view` en `RoleSeeder`/`PermissionSeeder` y rol `admin`.

### 4.8 Tests

- Seed datos, correr reporte por cada agrupación, assert agregados.
- Suma de cantidades y conteos correctos.
- Filtro por rango de fecha.
- `medic` sin `statistics.view` → 403.

---

## Orden de implementación recomendado (fases)

**Fase 1 — Citas (núcleo de datos)**
`appointments` migración + modelo + enum → servicio overlap → API → Filament → recordatorio job + notification → tests.

**Fase 2 — Medios del paciente**
`File` metadata migración + `patient.media` + upload/validation + API + Filament relation manager + tests.

**Fase 3 — Exámenes**
`examinations` migración + modelo + adjuntos + API + Filament + tests.

**Fase 4 — Catálogo marcas/laboratorios**
`laboratories`, `brands` (catálogo de sugerencias), pivot `brand_medicament`, seed + Filament CatalogResource. `recommended_brand` del pivot de prescripción se mantiene como string.

**Fase 5 — Estadísticas**
`medication_statistics` + listener/job + consultas + API reportes + Filament dashboards + permisos + tests.

**Fase 6 — Transversal**
Phone E.164, canal de notificación real, permisos nuevos en seeders, docs.

---

## Decisiones pendientes de confirmación

1. **Auth de paciente**: ¿el paciente tiene login para ver citas/recordatorios, o los recordatorios son solo envío 1-vía (email/WhatsApp) desde el médico?
2. **Panel de `medic`**: ¿abrir Filament a `medic` para gestionar citas/estadísticas, o toda la gestión queda en API?
3. **Canal de recordatorio**: ¿email (SMTP real), WhatsApp (Business API + canal package), o SMS (Twilio/Vonage)?
4. **Discos de medios**: ¿privado con descarga autenticada o público por intranet? Los videos pueden ser pesados.
5. **Marca vs laboratorio**: confirmar que `active_ingredient` es el principio activo y que el catálogo actual no trae marca/laboratorio (para dimensionar carga de datos).
6. **Estadísticas en tiempo real**: ¿dashboards reflejan cambios inmediatos (listener) o basta el job nocturno de reconciliación?
