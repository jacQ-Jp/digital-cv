<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 0;
        }

        html,
        body {
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 0;
            background: #fff;
            overflow: visible;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .pdf-stage {
            width: 210mm;
            min-height: 297mm;
            overflow: visible;
            background: #fff;
        }

        .cv-paper {
            width: 794px;
            min-height: 1123px;
            overflow: visible;
            background: #fff;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            font-size: 14.5px;
            line-height: 1.65;
        }

        .cv-paper > .sheet {
            max-width: 100% !important;
            min-height: 297mm !important;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .cv-paper > .cv-wrapper,
        .cv-paper > .cv-modern,
        .cv-paper > .cv-sidebar,
        .cv-paper > .cv-henry,
        .cv-paper > .cv-poster {
            min-height: 297mm !important;
        }
    </style>
</head>
<body>
    <div class="pdf-stage">
        <div class="cv-paper">
            @yield('content')
        </div>
    </div>
</body>
</html>
