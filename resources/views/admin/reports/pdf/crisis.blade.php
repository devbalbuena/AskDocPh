<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crisis Reports Log</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #111; }
        .header p { margin: 5px 0 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f8f9fa; font-weight: bold; color: #444; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status { font-weight: bold; text-transform: uppercase; font-size: 10px; padding: 2px 4px; border-radius: 3px; }
        .status-unresolved { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-resolved { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .meta { font-size: 10px; color: #666; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AskDocPH - Crisis Reports Log</h1>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="15%">Date/Time</th>
                <th width="20%">Reporter User</th>
                <th width="45%">Details & Notes</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>#CR-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                <td>
                    @if($report->user)
                        <strong>{{ $report->user->display_name }}</strong><br>
                        <span class="meta">{{ $report->user->email }}</span>
                    @else
                        Anonymous / Unknown
                    @endif
                </td>
                <td>
                    <em>Description:</em><br>
                    {{ $report->description ?? 'No description provided.' }}
                    
                    @if($report->admin_notes)
                        <br><br><em>Admin Notes:</em><br>
                        {{ $report->admin_notes }}
                    @endif
                </td>
                <td>
                    <span class="status status-{{ $report->status }}">{{ $report->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
