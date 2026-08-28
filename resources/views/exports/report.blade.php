<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; font-size: 12px; color: #12241f; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        .meta { margin-bottom: 16px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #0f4c3a; color: #fff; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">تاريخ الإصدار: {{ $generatedAt }}</div>
    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($headers as $header)
                        <td>{{ $row[$header] ?? '' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ max(count($headers), 1) }}">لا توجد بيانات</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
