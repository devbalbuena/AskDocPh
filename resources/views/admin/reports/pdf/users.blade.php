<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #111; }
        .header p { margin: 5px 0 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #444; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 10px; text-transform: uppercase; }
        .badge-admin { background-color: #f3e8ff; color: #7e22ce; }
        .badge-doctor { background-color: #dbeafe; color: #1d4ed8; }
        .badge-patient { background-color: #dcfce7; color: #15803d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AskDocPH - System Users Report</h1>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Verified</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->fname }} {{ $user->lname }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge badge-{{ $user->role }}">{{ $user->role }}</span>
                </td>
                <td>{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
