<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Digital CV Builder</title>
  </head>
  <body>
    <main style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; padding: 2rem;">
      <div class="container">
          <div class="py-4">
              <h1 class="display-6">Digital CV Builder</h1>
              <p class="text-muted">Buat CV digital dinamis dengan memilih template di awal, simpan sebagai draft atau publish untuk dibagikan.</p>
  
              @auth
                  <div class="d-flex flex-wrap gap-2">
                      <a class="btn btn-primary" href="{{ route('cv-builder.templates') }}">Start Build CV</a>
                      <a class="btn btn-outline-primary" href="{{ route('cvs.index') }}">My CVs</a>
                      @if(auth()->user()->role?->slug === 'admin')
                          <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Manage Templates</a>
                      @endif
                  </div>
              @else
                  <div class="alert alert-info mt-3">
                      Kamu perlu login / register untuk mulai membuat CV.
                  </div>
                  <div class="d-flex gap-2">
                      @if (Route::has('login'))
                          <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                      @endif
                      @if (Route::has('register'))
                          <a class="btn btn-outline-primary" href="{{ route('register') }}">Register</a>
                      @endif
                  </div>
              @endauth
          </div>
      </div>
    </main>
  </body>
</html>
