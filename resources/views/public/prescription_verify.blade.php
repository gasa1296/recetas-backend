<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validación Oficial de Prescripción #{{ $prescription->id }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --primary: #0f2b4a;
            --primary-light: #1e40af;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success-bg: #ecfdf5;
            --success-text: #065f46;
            --success-border: #a7f3d0;
            --warning-bg: #fffbeb;
            --warning-text: #92400e;
            --warning-border: #fde68a;
            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --danger-border: #fecaca;
            --info-bg: #f0fdf4;
            --info-text: #166534;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.5;
            padding: 16px;
        }

        .container {
            max-width: 820px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 24px 16px;
            margin-bottom: 20px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }

        .brand-badge svg {
            width: 14px;
            height: 14px;
        }

        .header h1 {
            font-size: 1.6rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        /* Status Banner */
        .status-banner {
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .status-valid {
            background-color: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .status-partial {
            background-color: var(--warning-bg);
            border-color: var(--warning-border);
            color: var(--warning-text);
        }

        .status-dispensed {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
        }

        .status-expired, .status-nulled {
            background-color: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger-text);
        }

        .status-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.7);
        }

        .status-icon svg {
            width: 22px;
            height: 22px;
        }

        .status-content h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .status-content p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title svg {
            width: 18px;
            height: 18px;
            color: var(--primary-light);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .info-group {
            margin-bottom: 12px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.95rem;
            color: var(--text-main);
            font-weight: 500;
        }

        .info-value.bold {
            font-weight: 700;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            word-break: break-all;
        }

        /* Signature notice card */
        .notice-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 14px;
            margin-top: 12px;
            font-size: 0.85rem;
            color: #0369a1;
            line-height: 1.45;
        }

        .hash-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 8px;
        }

        .btn-copy {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: var(--text-main);
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .btn-copy:hover {
            background: #f8fafc;
        }

        /* Medication table */
        .med-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .med-table th {
            text-align: left;
            background: #f8fafc;
            padding: 10px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
            border-bottom: 2px solid var(--border);
        }

        .med-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .med-name {
            font-weight: 700;
            color: var(--primary);
        }

        .med-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .med-brand {
            font-size: 0.8rem;
            color: #059669;
            font-style: italic;
            margin-top: 2px;
        }

        /* Action Buttons */
        .actions-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-top: 28px;
            margin-bottom: 36px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1a3f6b;
        }

        .btn-success {
            background-color: #059669;
            color: #ffffff;
        }

        .btn-success:hover {
            background-color: #047857;
        }

        .btn-outline {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: var(--text-main);
        }

        .btn-outline:hover {
            background-color: #f8fafc;
        }

        .footer {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            padding: 20px 0;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 640px) {
            body { padding: 12px; }
            .status-banner { flex-direction: column; align-items: flex-start; }
            .med-table th:nth-child(3), .med-table td:nth-child(3),
            .med-table th:nth-child(4), .med-table td:nth-child(4) {
                display: none;
            }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <div class="header">
        <div class="brand-badge">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
            </svg>
            Validación Criptográfica Oficial &middot; Conexión Segura SSL/TLS
        </div>
        <h1>Portal Oficial de Verificación de Prescripciones</h1>
        <p>{{ config('app.name', 'Sistema Médico') }} &mdash; Constatación de Autenticidad e Integridad en Línea</p>
    </div>

    {{-- Status Banner --}}
    @php
        $statusKey = (string) $prescription->status;
        $activeKey = (string) config('custom.prescription.status_keys.active');
        $partialKey = (string) config('custom.prescription.status_keys.partially_dispensed');
        $fullKey = (string) config('custom.prescription.status_keys.fully_dispensed');
        $expiredKey = (string) config('custom.prescription.status_keys.expired');
        $nulledKey = (string) config('custom.prescription.status_keys.nulled');

        $isExpired = $prescription->expires_at && now()->greaterThan($prescription->expires_at);
    @endphp

    @if($isExpired)
        <div class="status-banner status-expired">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="status-content">
                <h2>Prescripción Médica Vencida / Expirada</h2>
                <p>El período de vigencia clínica de este récipe finalizó el <strong>{{ $prescription->expires_at }}</strong>. No debe ser dispensado por farmacias.</p>
            </div>
        </div>
    @elseif($statusKey === $activeKey)
        <div class="status-banner status-valid">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="status-content">
                <h2>✓ Prescripción Médica Válida y Vigente</h2>
                <p>Documento oficial emitido y rubricado por un médico colegiado. Válido para dispensación en farmacia hasta el <strong>{{ $prescription->expires_at ?? 'Sin fecha límite' }}</strong>.</p>
            </div>
        </div>
    @elseif($statusKey === $partialKey)
        <div class="status-banner status-partial">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="status-content">
                <h2>Prescripción Dispensada Parcialmente</h2>
                <p>Se ha realizado una entrega fraccionada del tratamiento en farmacia. El remanente puede ser dispensado hasta el <strong>{{ $prescription->expires_at ?? 'N/A' }}</strong>.</p>
            </div>
        </div>
    @elseif($statusKey === $fullKey)
        <div class="status-banner status-dispensed">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="status-content">
                <h2>Prescripción Completamente Dispensada</h2>
                <p>El tratamiento indicado en este récipe ha sido suministrado en su totalidad. Este documento no puede ser reutilizado para nuevos despachos.</p>
            </div>
        </div>
    @elseif($statusKey === $nulledKey)
        <div class="status-banner status-nulled">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="status-content">
                <h2>Prescripción Médica Anulada</h2>
                <p>Esta prescripción fue cancelada formalmente por el médico emisor y carece de validez legal y clínica.</p>
            </div>
        </div>
    @else
        <div class="status-banner status-dispensed">
            <div class="status-content">
                <h2>Estado: {{ config("custom.prescription.status.{$prescription->status}", 'Registrada') }}</h2>
            </div>
        </div>
    @endif

    {{-- Cryptographic Integrity & Signature Card --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Certificación de Integridad y Firma Criptográfica
            </div>
            <span style="font-size:0.8rem;color:#059669;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#10b981;"></span>
                Firma Electrónica Verificada
            </span>
        </div>

        <div class="grid-2">
            <div class="info-group">
                <div class="info-label">Identificador de Récipe</div>
                <div class="info-value bold">#{{ $prescription->id }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Fecha de Emisión Oficial</div>
                <div class="info-value">{{ $prescription->updated_at }}</div>
            </div>
        </div>

        @if($prescription->prescription_hash)
            <div class="info-label" style="margin-top: 8px;">Huella Digital / Resumen Criptográfico (SHA-256)</div>
            <div class="hash-badge">
                <span class="mono" id="hashText">{{ $prescription->prescription_hash }}</span>
                <button type="button" class="btn-copy" onclick="copyHash()">Copiar</button>
            </div>
        @endif

        <div class="notice-box">
            <strong>Nota sobre la firma en visores PDF (Adobe Acrobat Reader):</strong>
            Este documento fue sellado y firmado criptográficamente con la clave institucional del profesional médico. Si su lector de PDF muestra una advertencia indicando que <em>"El emisor del certificado no es de confianza"</em>, se debe a que utiliza la infraestructura PKI propia de la institución y no una CA comercial privada. La validez legal, autenticidad, autoría e integridad inalterable se certifican oficialmente a través de este portal seguro bajo estándar SSL/TLS.
        </div>
    </div>

    {{-- Doctor & Patient Info --}}
    <div class="grid-2">
        {{-- Doctor --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Médico Prescriptor
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Profesional</div>
                <div class="info-value bold">{{ $prescription->user ? "{$prescription->user->first_name} {$prescription->user->last_name}" : 'N/A' }}</div>
            </div>
            @if($prescription->user?->identification)
                <div class="info-group">
                    <div class="info-label">Cédula / Identificación</div>
                    <div class="info-value mono">{{ $prescription->user->identification }}</div>
                </div>
            @endif
            <div class="info-group">
                <div class="info-label">Especialidad</div>
                <div class="info-value">{{ $prescription->specialty?->name ?? 'Medicina General' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Centro de Atención / Sala</div>
                <div class="info-value">{{ $prescription->room?->name ?? 'Consultorio' }}</div>
                @if($prescription->room?->address)
                    <div style="font-size:0.8rem;color:var(--text-muted);margin-top:2px;">{{ $prescription->room->address }}</div>
                @endif
            </div>
        </div>

        {{-- Patient --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Información del Paciente
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value bold">{{ $prescription->patient ? "{$prescription->patient->first_name} {$prescription->patient->last_name}" : 'N/A' }}</div>
            </div>
            @if($prescription->patient?->identification)
                <div class="info-group">
                    <div class="info-label">Identificación / Documento</div>
                    <div class="info-value mono">{{ $prescription->patient->identification }}</div>
                </div>
            @endif
            @if($prescription->diagnostic)
                <div class="info-group">
                    <div class="info-label">Diagnóstico Principal</div>
                    <div class="info-value">{{ $prescription->diagnostic }}</div>
                </div>
            @endif
            @if($prescription->allergy)
                <div class="info-group">
                    <div class="info-label" style="color:var(--danger-text);">Alergias Conocidas</div>
                    <div class="info-value bold" style="color:var(--danger-text);">{{ $prescription->allergy }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Medications Table --}}
    @if($prescription->medicaments && $prescription->medicaments->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Medicamentos Prescritos
                </div>
            </div>

            <table class="med-table">
                <thead>
                    <tr>
                        <th>Medicamento / Principio Activo</th>
                        <th>Posología</th>
                        <th>Frecuencia</th>
                        <th>Duración</th>
                        <th style="text-align:right;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->medicaments as $med)
                        <tr>
                            <td>
                                <div class="med-name">{{ $med->active_ingredient }}</div>
                                <div class="med-sub">{{ $med->type ?? '' }} {{ $med->group ? "({$med->group})" : '' }}</div>
                                @if(!empty($med->pivot->recommended_brand))
                                    <div class="med-brand">Sugerencia comercial: {{ $med->pivot->recommended_brand }}</div>
                                @endif
                            </td>
                            <td>{{ $med->pivot->dosage ?? '-' }}</td>
                            <td>{{ $med->pivot->frequency ?? '-' }}</td>
                            <td>{{ $med->pivot->duration ?? '-' }}</td>
                            <td style="text-align:right;font-weight:700;">{{ $med->pivot->medicament_quantity ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Pharmacy Dispense Panel (Interactive) --}}
    @if(($statusKey === $activeKey || $statusKey === $partialKey) && !$isExpired)
        <div class="card" style="border:1px solid #93c5fd;background:#f0f7ff;">
            <div class="card-header">
                <div class="card-title" style="color:#1d4ed8;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Gestión de Entrega para Farmacia
                </div>
            </div>
            <p style="font-size:0.9rem;color:#1e3a8a;margin-bottom:16px;">
                Si usted es el farmacéutico autorizado a cargo del despacho, puede registrar la entrega del tratamiento en el sistema:
            </p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <button type="button" class="btn btn-success" onclick="dispensePrescription('full')">
                    ✓ Registrar Despacho Total
                </button>
                <button type="button" class="btn btn-outline" style="border-color:#93c5fd;color:#1d4ed8;" onclick="dispensePrescription('partial')">
                    Registrar Despacho Parcial
                </button>
            </div>
            <div id="dispenseMessage" style="display:none;margin-top:12px;font-size:0.9rem;font-weight:600;"></div>
        </div>
    @endif

    {{-- Actions Bar --}}
    <div class="actions-bar">
        <a href="{{ $pdfUrl }}" class="btn btn-primary" download>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar Documento PDF Firmado
        </a>
        <button type="button" class="btn btn-outline" onclick="window.print()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"/></svg>
            Imprimir Constancia
        </button>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Sistema Médico') }}. Todos los derechos reservados.</p>
        <p style="margin-top:4px;">Este portal opera bajo protocolos criptográficos para la verificación segura de recetas médicas electrónicas.</p>
    </div>
</div>

<script>
    function copyHash() {
        const text = document.getElementById('hashText').innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            alert('Hash SHA-256 copiado al portapapeles');
        });
    }

    function dispensePrescription(mode) {
        const label = mode === 'full' ? 'TOTAL' : 'PARCIAL';
        if (!confirm('¿Confirmar el registro de despacho ' + label + ' para esta prescripción médica?')) {
            return;
        }

        const msgDiv = document.getElementById('dispenseMessage');
        msgDiv.style.display = 'block';
        msgDiv.style.color = '#1e3a8a';
        msgDiv.innerText = 'Procesando registro de dispensación...';

        fetch('{{ $dispenseUrl }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ mode: mode })
        })
        .then(async (response) => {
            const data = await response.json();
            if (response.ok) {
                msgDiv.style.color = '#059669';
                msgDiv.innerText = '✓ Despacho registrado exitosamente. Recargando estado...';
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            } else {
                msgDiv.style.color = '#dc2626';
                msgDiv.innerText = 'Error: ' + (data.message || 'No se pudo procesar el despacho.');
            }
        })
        .catch((error) => {
            msgDiv.style.color = '#dc2626';
            msgDiv.innerText = 'Error de comunicación con el servidor.';
        });
    }
</script>
</body>
</html>
