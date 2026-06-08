<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-left h2 {
            margin: 0 0 5px;
            color: #2563eb;
        }
        .header-left p {
            margin: 2px 0;
            font-size: 11px;
        }
        .header-right {
            text-align: right;
            font-size: 11px;
        }
        .header-right strong {
            font-size: 14px;
        }
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .grid-2 {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .grid-2 > div {
            flex: 1 1 45%;
        }
        .field {
            margin-bottom: 4px;
            font-size: 11px;
        }
        .field-label {
            font-weight: bold;
            display: inline;
        }
        .field-value {
            display: inline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table th {
            background: #2563eb;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tr:nth-child(even) td {
            background: #f9fafb;
        }
        .vital-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .vital-item {
            flex: 0 0 15%;
            text-align: center;
            padding: 6px 8px;
            background: #f0f9ff;
            border-radius: 4px;
            border: 1px solid #bae6fd;
        }
        .vital-item .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        .vital-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .signature-area {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            width: 250px;
            text-align: center;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-allergy {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
        .badge-none {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>
<body>

    @php
        $doctor = $prescription->user;
        $patient = $prescription->patient;
        $room = $prescription->room;
    @endphp

    <div class="header">
        <div class="header-left">
            <h2>{{ $doctor->first_name }} {{ $doctor->last_name1 }} {{ $doctor->last_name2 }}</h2>
            <p>{{ $prescription->specialty?->name ?? 'General Medicine' }}</p>
            <p>{{ $prescription->specialty?->identification ?? '' }}</p>
            @if ($room)
                <p>{{ $room->name }} &mdash; {{ $room->address }}</p>
                <p>{{ $room->street }}, {{ $room->colony }}, {{ $room->state }}</p>
            @endif
        </div>
        <div class="header-right">
            <strong>PRESCRIPTION</strong>
            <p>Date: {{ $prescription->created_at->format('d/m/Y') }}</p>
            <p>#{{ $prescription->id }}</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="grid-2">
            <div>
                <div class="field">
                    <span class="field-label">Name:</span>
                    <span class="field-value">{{ $patient->first_name }} {{ $patient->last_name1 }} {{ $patient->last_name2 }}</span>
                </div>
                <div class="field">
                    <span class="field-label">Email:</span>
                    <span class="field-value">{{ $patient->email ?? 'N/A' }}</span>
                </div>
            </div>
            <div>
                <div class="field">
                    <span class="field-label">Phone:</span>
                    <span class="field-value">{{ is_array($patient->phone) ? implode(', ', $patient->phone) : ($patient->phone ?? 'N/A') }}</span>
                </div>
                <div class="field">
                    <span class="field-label">Gender:</span>
                    <span class="field-value">{{ $patient->gender ?? 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="field-label">Birth Date:</span>
                    <span class="field-value">{{ $patient->birth_date ? $patient->birth_date->format('d/m/Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Vital Signs</div>
        <div class="vital-grid">
            <div class="vital-item">
                <div class="label">Temp</div>
                <div class="value">{{ $prescription->temp ?? '--' }}</div>
            </div>
            <div class="vital-item">
                <div class="label">Weight</div>
                <div class="value">{{ $prescription->weight ?? '--' }}</div>
            </div>
            <div class="vital-item">
                <div class="label">Height</div>
                <div class="value">{{ $prescription->height ?? '--' }}</div>
            </div>
            <div class="vital-item">
                <div class="label">Pressure</div>
                <div class="value">{{ $prescription->pressure ?? '--' }}</div>
            </div>
            <div class="vital-item">
                <div class="label">Saturation</div>
                <div class="value">{{ $prescription->saturation ?? '--' }}</div>
            </div>
            <div class="vital-item">
                <div class="label">PPM</div>
                <div class="value">{{ $prescription->ppm ?? '--' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Diagnostic</div>
        <p style="margin: 4px 0; font-size: 12px;">{{ $prescription->diagnostic ?? 'N/A' }}</p>
    </div>

    <div class="section">
        <div class="section-title">Allergies</div>
        <p style="margin: 4px 0;">
            @if ($prescription->allergy)
                <span class="badge badge-allergy">{{ $prescription->allergy }}</span>
            @else
                <span class="badge badge-none">No known allergies</span>
            @endif
        </p>
    </div>

    <div class="section">
        <div class="section-title">Prescribed Medicaments</div>
        <table>
            <thead>
                <tr>
                    <th>Medicament</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prescription->medicaments as $medicament)
                    <tr>
                        <td>{{ $medicament->name }}</td>
                        <td>{{ $medicament->pivot->dosage }}</td>
                        <td>{{ $medicament->pivot->frequency }}</td>
                        <td>{{ $medicament->pivot->duration }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No medicaments prescribed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($prescription->diet)
        <div class="section">
            <div class="section-title">Diet</div>
            <p style="margin: 4px 0; font-size: 11px;">{{ $prescription->diet }}</p>
        </div>
    @endif

    @if ($prescription->comments)
        <div class="section">
            <div class="section-title">Additional Comments</div>
            <p style="margin: 4px 0; font-size: 11px;">{{ $prescription->comments }}</p>
        </div>
    @endif

    <div class="signature-area">
        _________________________________<br>
        {{ $doctor->first_name }} {{ $doctor->last_name1 }} {{ $doctor->last_name2 }}<br>
        {{ $prescription->specialty?->name ?? 'General Medicine' }}
    </div>

    <div class="footer">
        This prescription was generated electronically on {{ $prescription->created_at->format('d/m/Y H:i') }}.
    </div>

</body>
</html>
