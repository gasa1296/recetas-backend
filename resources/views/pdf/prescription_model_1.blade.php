<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #1e293b; padding: 24px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 0; }
        .hdr { border-bottom: 2px solid #0f2b4a; padding-bottom: 8px; margin-bottom: 16px; }
        .hdr h1 { font-size: 16pt; color: #0f2b4a; margin: 0; }
        .hdr-sub { font-size: 9pt; color: #64748b; margin-top: 4px; }
        .card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 8px; }
        .card-title { font-size: 9pt; font-weight: 700; color: #0f2b4a; margin-bottom: 12px; }
        .lbl { font-size: 7pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; letter-spacing: 0.08em; display: block; }
        .val { font-size: 10pt; }
        .mono { font-family: 'Courier New', monospace; }
        .fw { font-weight: 700; }
        .gi { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 10px; text-align: center; }
        .gv { font-size: 9pt; font-weight: 700; color: #0f2b4a; }
        .gl { font-size: 7pt; color: #0f2b4a; text-transform: uppercase; display: block; margin-top: 2px; }
        .tbl-lbl { font-size: 7pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; display: block; }
        .tbl-val { font-size: 9pt; }
        th { font-size: 8pt; font-weight: 700; color: #0f2b4a; text-transform: uppercase; text-align: left; padding: 6px; border-bottom: 2px solid #0f2b4a; }
        td.med { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; }
        .notes { white-space: pre-wrap; font-size: 9pt; color: #1e293b; }
        .sig { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .sig-line { width: 200px; border-top: 1px solid #1e293b; margin-bottom: 4px; }
        .sig-lbl { font-size: 9pt; color: #64748b; }
        .sig-img { max-width: 200px; max-height: 60px; margin-bottom: 4px; }
        .meta { font-size: 8pt; color: #94a3b8; margin-top: 16px; }
        .hash { font-family: 'Courier New', monospace; font-size: 7pt; color: #94a3b8; margin-top: 4px; }
        .danger { color: #dc2626; }
        .mb-8 { margin-bottom: 8px; }
        .qr img { width: 80px; height: 80px; }
        .p-8 { padding: 8px; }
        .w-50 { width: 50%; }
        .vital-cell { width: 16.66%; padding: 4px; }
    </style>
</head>
<body>
    @php
        $phone = function ($p) { return is_array($p) ? implode(', ', $p) : ($p ?? ''); };
        $gender = function ($g) { return $g === 'M' ? 'Male' : ($g === 'F' ? 'Female' : ($g ?? 'Other')); };
        $v = function ($val, $suf = '') { return is_null($val) ? '-' : ($val / 100) . $suf; };
        $vitals = [['temp','Temp',' °C'],['weight','Weight',' kg'],['height','Height',' cm'],['pressure','BP',''],['saturation','O2 Sat.',' %'],['ppm','Pulse','']];
        $hv = false; foreach ($vitals as $x) { if (!is_null($prescription->{$x[0]})) { $hv = true; break; } }
    @endphp

    {{-- Header with QR --}}
    <table class="hdr"><tr>
        <td style="width:80%;">
            <h1>Prescription Details</h1>
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
                <div class="card-title">Patient Info</div>
                <div class="mb-8"><span class="lbl">Full Name</span><span class="val fw">{{ $prescription->patient->first_name }} {{ $prescription->patient->last_name }}</span></div>
                <div class="mb-8"><span class="lbl">Identification</span><span class="mono">{{ $prescription->patient->identification }}</span></div>
                <div class="mb-8"><span class="lbl">Gender</span><span class="val">{{ $gender($prescription->patient->gender) }}</span></div>
                <div class="mb-8"><span class="lbl">Birth Date</span><span class="val">{{ $prescription->patient->birth_date }}</span></div>
                @if($prescription->patient->email)<div class="mb-8"><span class="lbl">Email</span><span class="val">{{ $prescription->patient->email }}</span></div>@endif
                @if($prescription->patient->phone)<div class="mb-8"><span class="lbl">Phone</span><span class="val">{{ $phone($prescription->patient->phone) }}</span></div>@endif
            </div>
        </td>
        <td class="w-50 p-8">
            <div class="card">
                <div class="card-title">Prescriber Info</div>
                <div class="mb-8"><span class="lbl">Doctor</span><span class="val fw">{{ $prescription->user->first_name }} {{ $prescription->user->last_name }}</span></div>
                @if($prescription->user->identification)<div class="mb-8"><span class="lbl">ID</span><span class="mono">{{ $prescription->user->identification }}</span></div>@endif
                <div class="mb-8"><span class="lbl">Room</span><span class="val">{{ $prescription->room->name }}</span></div>
                <div class="mb-8"><span class="lbl">Specialty</span><span class="val">{{ $prescription->specialty->name }}</span></div>
            </div>
        </td>
    </tr></table>

    {{-- Vital Signs --}}
    @if($hv)
        <div class="card">
            <div class="card-title">Vital Signs</div>
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
        <div class="card-title">Diagnosis &amp; Treatment</div>
        @if($prescription->allergy)<div class="mb-8"><span class="lbl">Allergies</span><span class="val" style="color:#dc2626;">{{ $prescription->allergy }}</span></div>@endif
        <div class="mb-8"><span class="lbl">Diagnostic</span><div class="notes">{{ $prescription->diagnostic ?? '-' }}</div></div>
        @if($prescription->diet)<div class="mb-8"><span class="lbl">Diet</span><div class="notes">{{ $prescription->diet }}</div></div>@endif
    </div>

    {{-- Medications --}}
    @if($prescription->medicaments->isNotEmpty())
        <div class="card">
            <div class="card-title">Medication Orders</div>
            <table>
                <tr><th>Medication</th><th style="text-align:center;">Dosage</th><th style="text-align:center;">Freq</th><th style="text-align:center;">Duration</th><th style="text-align:center;">Qty</th></tr>
                @foreach($prescription->medicaments as $med)
                    <tr>
                        <td class="med"><span class="fw">{{ $med->active_ingredient }}</span><br><span style="font-size:8pt;color:#0f2b4a;">{{ $med->type }} - {{ $med->group }}</span></td>
                        <td class="med" style="text-align:center;">@if($med->pivot->dosage)<span class="tbl-lbl">Dosage</span><span class="tbl-val">{{ $med->pivot->dosage }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->frequency)<span class="tbl-lbl">Freq</span><span class="tbl-val">{{ $med->pivot->frequency }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->duration)<span class="tbl-lbl">Duration</span><span class="tbl-val">{{ $med->pivot->duration }}</span>@endif</td>
                        <td class="med" style="text-align:center;">@if($med->pivot->medicament_quantity)<span class="tbl-lbl">Qty</span><span class="tbl-val">{{ $med->pivot->medicament_quantity }}</span>@endif</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    {{-- Comments --}}
    @if($prescription->comments)
        <div class="card">
            <div class="card-title">Additional Notes</div>
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
        <div class="sig-lbl">Doctor's Signature</div>
    </div>

    {{-- Meta --}}
    <div class="meta">
        <span>Date: {{ $prescription->created_at }}</span>
        @if($prescription->expires_at)<span class="danger">Expires: {{ $prescription->expires_at }}</span>@endif
    </div>
    @if($prescription->prescription_hash)<div class="hash">{{ $prescription->prescription_hash }}</div>@endif
</body>
</html>
