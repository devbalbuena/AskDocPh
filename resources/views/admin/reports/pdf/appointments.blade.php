<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointments Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #111; }
        .header p { margin: 5px 0 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #444; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status { font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .status-pending { color: #d97706; }
        .status-confirmed { color: #2563eb; }
        .status-completed { color: #16a34a; }
        .status-cancelled { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AskDocPH - Appointments Report</h1>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Date</th>
                <th>Time Window</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $apt)
            <tr>
                <td>#{{ $apt->id }}</td>
                <td>{{ $apt->doctor->display_name ?? 'N/A' }}</td>
                <td>{{ $apt->patient->display_name ?? 'N/A' }}</td>
                <td>{{ $apt->appointment_date }}</td>
                <td>{{ \Carbon\Carbon::parse($apt->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($apt->end_time)->format('H:i') }}</td>
                <td><span class="status status-{{ $apt->status }}">{{ $apt->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
