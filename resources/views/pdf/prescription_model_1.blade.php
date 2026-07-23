<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescripción #{{ $prescription->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #1e293b; padding: 24px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 0; }
        .hdr { border-bottom: 2px solid #0f2b4a; padding-bottom: 4px; margin-bottom: 8px; }
        .hdr h1 { font-size: 16pt; color: #0f2b4a; margin: 0; }
        .hdr-sub { font-size: 9pt; color: #64748b; margin-top: 4px; }
        .card { border-radius: 8px; padding: 16px; margin-bottom: 4px; }
        .card-title { font-size: 9pt; font-weight: 700; color: #0f2b4a; margin-bottom: 12px; }
        .lbl { font-size: 7pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; letter-spacing: 0.08em; display: block; }
        .val { font-size: 10pt; }
        .mono { font-family: 'Courier New', monospace; }
        .fw { font-weight: 700; }
        .gi { background: #f8fafc; border-radius: 8px; padding: 3px 5px; text-align: center; }
        .gv { font-size: 9pt; font-weight: 700; color: #0f2b4a; }
        .gl { font-size: 7pt; color: #0f2b4a; text-transform: uppercase; display: block; margin-top: 2px; }
        .tbl-lbl { font-size: 7pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; display: block; }
        .tbl-val { font-size: 9pt; }
        th { font-size: 8pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; text-align: left; padding: 6px; border-bottom: 2px solid #0f2b4a; }
        td.med { padding: 4px 3px; }
        .notes { white-space: pre-wrap; font-size: 9pt; color: #1e293b; }
        .sig { margin-top: 12px; padding-top: 8px; }
        .sig-line { width: 200px; border-top: 1px solid #1e293b; margin-bottom: 4px; }
        .sig-lbl { font-size: 9pt; color: #64748b; }
        .sig-img { max-width: 200px; max-height: 60px; margin-bottom: 4px; }
        .meta { font-size: 8pt; color: #94a3b8; margin-top: 8px; }
        .hash { font-family: 'Courier New', monospace; font-size: 7pt; color: #94a3b8; margin-top: 4px; }
        .danger { color: #dc2626; }
        .mb-8 { margin-bottom: 4px; }
        .qr img { width: 80px; height: 80px; }
        .p-8 { padding: 2px; }
        .w-50 { width: 50%; }
        .vital-cell { width: 16.66%; padding: 4px; }
    </style>
</head>
<body>
        @php
        $phone = function ($p) { return is_array($p) ? implode(', ', $p) : ($p ?? ''); };
        $gender = function ($g) { return $g === 'M' ? 'Masculino' : ($g === 'F' ? 'Femenino' : ($g ?? 'Otro')); };
        $v = function ($val, $suf = '') { return is_null($val) ? '-' : ($val / 100) . $suf; };
        $vitals = [['temp','Temperatura',' °C'],['weight','Peso',' kg'],['height','Altura',' cm'],['pressure','Tensión',''],['saturation','Saturación O2',' %'],['ppm','Pulso','']];
        $hv = false; foreach ($vitals as $x) { if (!is_null($prescription->{$x[0]})) { $hv = true; break; } }
    @endphp

    {{-- Header with QR --}}
    <table class="hdr"><tr>
        <td style="width:80%;">
            <h1>Detalles de la Prescripción</h1>
            <div class="hdr-sub">{{ $prescription->room->name }} &middot; {{ $prescription->specialty->name }}</div>
        </td>
        <td style="width:20%;text-align:right;" class="qr">
            @if($qrCode)
                <img src="{{ $qrCode }}" alt="QR">
            @endif
        </td>
    </tr></table>

    {{-- Patient & Prescriber --}}
    <table><tr>
        <td class="w-50 p-8">
            <div class="card">
                <div class="card-title">Información del Paciente</div>
                <div class="mb-8"><span class="lbl">Nombre completo</span><span class="val fw">{{ $prescription->patient->first_name }} {{ $prescription->patient->last_name }}</span></div>
                <div class="mb-8"><span class="lbl">Identificación</span><span class="mono">{{ $prescription->patient->identification }}</span></div>
                <div class="mb-8"><span class="lbl">Sexo</span><span class="val">{{ $gender($prescription->patient->gender) }}</span></div>
                <div class="mb-8"><span class="lbl">Fecha de nacimiento</span><span class="val">{{ $prescription->patient->birth_date }}</span></div>
                @if($prescription->patient->email)<div class="mb-8"><span class="lbl">Correo electrónico</span><span class="val">{{ $prescription->patient->email }}</span></div>@endif
                @if($prescription->patient->phone)<div class="mb-8"><span class="lbl">Teléfono</span><span class="val">{{ $phone($prescription->patient->phone) }}</span></div>@endif
            </div>
        </td>
        <td class="w-50 p-8">
            <div class="card">
                <div class="card-title">Información del Prescriptor</div>
                    <div class="mb-8"><span class="lbl">Médico</span><span class="val fw">{{ $prescription->user->first_name }} {{ $prescription->user->last_name }}</span></div>
                    @if($prescription->user->identification)<div class="mb-8"><span class="lbl">Identificación</span><span class="mono">{{ $prescription->user->identification }}</span></div>@endif
                    <div class="mb-8"><span class="lbl">Sala</span><span class="val">{{ $prescription->room->name }}</span></div>
                    <div class="mb-8"><span class="lbl">Especialidad</span><span class="val">{{ $prescription->specialty->name }}</span></div>
            </div>
        </td>
    </tr></table>

    {{-- Vital Signs --}}
    @if($hv)
        <div class="card">
            <div class="card-title">Signos Vitales</div>
            <table><tr>
                @foreach($vitals as $x)
                    @if(!is_null($prescription->{$x[0]}))
                        <td class="vital-cell"><div class="gi"><div class="gv">{{ $v($prescription->{$x[0]}, $x[2]) }}</div><span class="gl">{{ $x[1] }}</span></div></td>
                    @endif
                @endforeach
            </tr></table>
        </div>
    @endif

    {{-- Diagnosis --}}
    <div class="card">
        <div class="card-title">Diagnóstico y Tratamiento</div>
        @if($prescription->allergy)<div class="mb-8"><span class="lbl">Alergias</span><span class="val" style="color:#dc2626;">{{ $prescription->allergy }}</span></div>@endif
        <div class="mb-8"><span class="lbl">Diagnóstico</span><div class="notes">{{ $prescription->diagnostic ?? '-' }}</div></div>
        @if($prescription->diet)<div class="mb-8"><span class="lbl">Dieta</span><div class="notes">{{ $prescription->diet }}</div></div>@endif
    </div>

    {{-- Medications --}}
    @if($prescription->medicaments->isNotEmpty())
        <div class="card">
            <div class="card-title">Prescripciones de Medicamentos</div>
            <table>
                <tr><th>Medicamento</th><th style="text-align:center;">Dosis</th><th style="text-align:center;">Frecuencia</th><th style="text-align:center;">Duración</th><th style="text-align:center;">Cantidad</th></tr>
                @foreach($prescription->medicaments as $med)
                    <tr>
                        <td class="med"><span class="fw">{{ $med->active_ingredient }}</span><br><span style="font-size:8pt;color:#0f2b4a;">{{ $med->type }} - {{ $med->group }}</span></td>
                        <td class="med" style="text-align:center;">@if($med->pivot->dosage)<span class="tbl-lbl">Dosis</span><span class="tbl-val">{{ $med->pivot->dosage }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->frequency)<span class="tbl-lbl">Frecuencia</span><span class="tbl-val">{{ $med->pivot->frequency }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->duration)<span class="tbl-lbl">Duración</span><span class="tbl-val">{{ $med->pivot->duration }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->medicament_quantity)<span class="tbl-lbl">Cantidad</span><span class="tbl-val">{{ $med->pivot->medicament_quantity }}</span>@endif</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    {{-- Comments --}}
    @if($prescription->comments)
        <div class="card">
            <div class="card-title">Notas Adicionales</div>
            <div class="notes">{{ $prescription->comments }}</div>
        </div>
    @endif

    {{-- Signature --}}
    <div class="sig">
        <div class="fw" style="font-size:10pt;">{{ $prescription->user->first_name }} {{ $prescription->user->last_name }}</div>
        @if($signature)
            <img src="data:image/png;base64,{{ $signature }}" alt="Signature" class="sig-img">
        @else
            <div class="sig-line"></div>
        @endif
        <div class="sig-lbl">Firma del Médico</div>
    </div>

    {{-- Meta --}}
    <div class="meta">
        <span>Fecha de creación: {{ $prescription->updated_at }}</span>
        @if($prescription->expires_at)<span class="danger">Fecha de expiración: {{ $prescription->expires_at }}</span>@endif
    </div>
    @if($prescription->prescription_hash)<div class="hash">{{ $prescription->prescription_hash }}</div>@endif
</body>
</html>
