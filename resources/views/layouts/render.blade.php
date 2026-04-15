<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $cv->title ?? 'CV' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{background:#f3f4f6;padding:40px 20px}
        .toolbar{max-width:794px;margin:0 auto 16px;display:flex;justify-content:flex-end;gap:8px}
        .toolbar button{display:inline-flex;align-items:center;gap:6px;height:38px;padding:0 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid #e2e8f0;background:#fff;color:#334155;font-family:inherit}
        .toolbar button:hover{background:#f8fafc}
        .toolbar .bp{background:#0f172a;color:#fff;border-color:#0f172a}
        .cv-paper{width:794px;min-height:1123px;margin:0 auto;background:#fff;padding:48px 52px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 6px 24px rgba(0,0,0,.04);font-family:'Inter',sans-serif;color:#1f2937;font-size:14.5px;line-height:1.65}
        @media print{body{background:#fff;padding:0;margin:0}.cv-paper{width:100%;min-height:auto;padding:0;margin:0;box-shadow:none}.toolbar{display:none!important}@page{size:A4;margin:12mm}}
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="history.back()">← Kembali</button>
        <button class="bp" onclick="window.print()">Print / PDF</button>
    </div>
    <article class="cv-paper">
        @yield('content')
    </article>
</body>
</html>