<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        html,body{width:100%;height:100%}
        body{margin:0;padding:0;background:#fff;overflow:hidden}
        .thumb-stage{position:relative;width:100%;height:100%;overflow:hidden;background:#fff}
        .cv-paper{transform-origin:top left;will-change:transform}
    </style>
</head>
<body>
    <div class="thumb-stage">
        <div id="thumbPaper" class="cv-paper" style="width:794px;min-height:1123px;background:#fff;font-family:'Inter',sans-serif;color:#1f2937;font-size:14.5px;line-height:1.65;">
            @yield('content')
        </div>
    </div>

    <script>
        (() => {
            const baseWidth = 794;
            const baseHeight = 1123;
            const paper = document.getElementById('thumbPaper');
            const stage = document.querySelector('.thumb-stage');

            if (!paper || !stage) return;

            const fit = () => {
                const viewportWidth = window.innerWidth || baseWidth;
                const viewportHeight = window.innerHeight || baseHeight;
                const scale = Math.min(viewportWidth / baseWidth, viewportHeight / baseHeight);

                paper.style.transform = `scale(${Math.max(scale, 0.1)})`;
                paper.style.width = `${baseWidth}px`;
                paper.style.minHeight = `${baseHeight}px`;
            };

            window.addEventListener('resize', fit);
            fit();
        })();
    </script>
</body>
</html>