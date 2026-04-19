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
        .cv-paper{width:794px;min-height:1123px;margin:0 auto;background:#fff;padding:0;overflow:visible;box-shadow:0 1px 3px rgba(0,0,0,.06),0 6px 24px rgba(0,0,0,.04);font-family:'Inter',sans-serif;color:#1f2937;font-size:14.5px;line-height:1.65}
        .cv-paper>.sheet,
        .cv-paper>.cv-modern,
        .cv-paper>.cv-sidebar,
        .cv-paper>.cv-henry,
        .cv-paper>.cv-poster{min-height:1123px}
        html,body{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        @media print{
            @page{size:A4;margin:0}
            html,body{width:210mm;min-height:297mm;margin:0;padding:0;background:#fff;overflow:visible}
            .toolbar{display:none!important}
            .cv-paper{width:794px;height:1123px;min-height:1123px;padding:0;margin:0 auto;box-shadow:none;overflow:visible;break-inside:auto;page-break-inside:auto}
            .cv-paper>.sheet{max-width:100%!important;min-height:1123px!important;margin:0!important;border-radius:0!important;box-shadow:none!important}
            .cv-paper>.cv-modern,
            .cv-paper>.cv-sidebar,
            .cv-paper>.cv-henry,
            .cv-paper>.cv-poster{min-height:1123px!important}
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.location.href='{{ route('cvs.index') }}'">← Kembali</button>
        <button class="bp" onclick="window.print()">Print / PDF</button>
    </div>
    <article class="cv-paper">
        @yield('content')
    </article>
</body>
</html>