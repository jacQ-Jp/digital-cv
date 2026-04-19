@php
    $triedRegister = old('name') || $errors->has('name') || $errors->has('password_confirmation');
    $triedLogin = ! $triedRegister && ($errors->has('email') || $errors->has('password'));
    $requestedPanel = ($activePanel ?? (request()->routeIs('register') ? 'register' : 'login'));
    $requestedPanel = $requestedPanel === 'register' ? 'register' : 'login';
    $currentPanel = $triedRegister ? 'register' : $requestedPanel;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar - CV Builder Platform</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #050505;
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        .lp-bg-orb { position: absolute; border-radius: 50%; pointer-events: none; z-index: 0; }
        .lp-bg-orb-1 { width: 450px; height: 450px; top: -14%; left: -10%; background: radial-gradient(circle, rgba(124,58,237,.1), transparent 60%); filter: blur(60px); }
        .lp-bg-orb-2 { width: 380px; height: 380px; bottom: -12%; right: -8%; background: radial-gradient(circle, rgba(168,85,247,.07), transparent 60%); filter: blur(60px); }
        .lp-bg-grid { position: absolute; inset: 0; opacity: .015; background-image: linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 70px 70px; pointer-events: none; z-index: 0; }

        .lp-nav {
            position: relative;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2rem;
            backdrop-filter: blur(10px);
            background: rgba(8, 5, 26, 0.8);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .lp-logo { font-size: 1.25rem; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.03em; display: flex; align-items: center; gap: 0.5rem; }
        .lp-logo span { color: #a78bfa; }
        .lp-links { display: flex; gap: 2rem; list-style: none; }
        .lp-links a { text-decoration: none; color: rgba(255,255,255,.7); font-size: 0.9rem; font-weight: 500; transition: color 0.3s; }
        .lp-links a:hover { color: #a78bfa; }

        .lp-btn-nav {
            padding: 0.6rem 1.4rem;
            background: linear-gradient(135deg,#7c3aed,#9333ea);
            color: #fff;
            text-decoration: none;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: box-shadow 0.3s, transform 0.2s;
        }

        .lp-btn-nav:hover { box-shadow: 0 4px 15px rgba(124,58,237,.4); transform: translateY(-1px); }
        @media (max-width: 768px) { .lp-links { display: none; } }

        .lp-main { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; padding: 2rem 1.25rem; z-index: 10; }

        .lp-box {
            position: relative;
            width: 100%;
            max-width: 900px;
            min-height: 640px;
            background: rgba(255,255,255,.035);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
            animation: lpEntry .8s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes lpEntry { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: none; } }

        .lp-panels { display: flex; min-height: 100%; position: relative; z-index: 1; }
        .lp-pnl { width: 50%; display: flex; align-items: center; justify-content: center; padding: 2.5rem 2rem; }
        .lp-pnl-in { width: 100%; max-width: 310px; }

        .lp-h { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0 0 .25rem; letter-spacing: -.02em; }
        .lp-sub { font-size: .82rem; color: rgba(255,255,255,.3); margin: 0 0 1.1rem; line-height: 1.55; }

        .lp-alert {
            border: 1px solid rgba(16,185,129,.38);
            background: rgba(16,185,129,.12);
            color: #a7f3d0;
            border-radius: 9px;
            padding: .58rem .65rem;
            font-size: .75rem;
            margin-bottom: .75rem;
        }

        .lp-fg { margin-bottom: 1.1rem; }
        .lp-fg label { display: block; font-size: .68rem; font-weight: 600; color: rgba(255,255,255,.35); margin-bottom: .3rem; text-transform: uppercase; letter-spacing: .06em; }
        .lp-iw { position: relative; display: flex; align-items: center; }
        .lp-ic { position: absolute; left: 12px; color: rgba(255,255,255,.15); width: 16px; height: 16px; pointer-events: none; }

        .lp-in {
            width: 100%;
            padding: .62rem .75rem .62rem 2.5rem;
            border: 1.5px solid rgba(255,255,255,.07);
            border-radius: 11px;
            font-size: .85rem;
            color: #fff;
            background: rgba(255,255,255,.035);
            outline: none;
            transition: border-color .3s, box-shadow .3s;
            font-family: inherit;
        }

        .lp-in::placeholder { color: rgba(255,255,255,.18); }
        .lp-in:focus { border-color: rgba(168,85,247,.4); box-shadow: 0 0 0 3px rgba(168,85,247,.08); }
        .lp-in.lp-err-input,
        .lp-in.lp-err-input:focus { border-color: rgba(239,68,68,.4); box-shadow: 0 0 0 3px rgba(239,68,68,.06); }

        .lp-ptog { position: absolute; right: 11px; background: none; border: none; color: rgba(255,255,255,.18); cursor: pointer; padding: 4px; display: flex; align-items: center; }
        .lp-ptog:hover { color: rgba(255,255,255,.45); }
        .lp-ptog svg { width: 16px; height: 16px; display: block; }

        .lp-err { font-size: .75rem; color: #f87171; margin-top: .25rem; padding-left: 2px; min-height: 1.2em; }

        .lp-ck { display: flex; align-items: center; gap: .5rem; cursor: pointer; margin-bottom: 1.4rem; user-select: none; }
        .lp-ck input { position: absolute; opacity: 0; width: 0; height: 0; }
        .lp-ckb { width: 17px; height: 17px; border-radius: 5px; border: 1.5px solid rgba(255,255,255,.1); background: rgba(255,255,255,.035); display: flex; align-items: center; justify-content: center; }
        .lp-ckb svg { width: 11px; height: 11px; color: #fff; opacity: 0; transform: scale(.4); transition: all .2s; }
        .lp-ck input:checked + .lp-ckb { background: #7c3aed; border-color: #7c3aed; box-shadow: 0 2px 8px rgba(124,58,237,.35); }
        .lp-ck input:checked + .lp-ckb svg { opacity: 1; transform: scale(1); }
        .lp-ckt { font-size: .82rem; color: rgba(255,255,255,.3); }

        .lp-btn {
            width: 100%;
            padding: .72rem;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg,#7c3aed,#9333ea);
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: box-shadow .3s, transform .15s;
            font-family: inherit;
        }

        .lp-btn:hover { box-shadow: 0 6px 24px rgba(124,58,237,.3); transform: translateY(-1px); }

        .lp-btm { text-align: center; margin-top: 1.1rem; font-size: .82rem; color: rgba(255,255,255,.25); }
        .lp-btm a { color: #a78bfa; text-decoration: none; font-weight: 600; transition: color .2s; cursor: pointer; }
        .lp-btm a:hover { color: #c4b5fd; }

        .lp-ov {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg,#0c0518 0%,#140d22 100%);
            transition: left .8s cubic-bezier(.65,0,.35,1);
            z-index: 10;
            overflow: hidden;
            border-radius: 28px 0 0 28px;
        }

        .lp-ov.lp-at-l { left: 0; border-radius: 0 28px 28px 0; }

        .lp-ov-c1, .lp-ov-c2 { position: absolute; border-radius: 50%; border: 1px solid rgba(255,255,255,.04); pointer-events: none; }
        .lp-ov-c1 { width: 180px; height: 180px; top: -30px; right: -50px; }
        .lp-ov-c2 { width: 120px; height: 120px; bottom: -25px; left: -30px; }
        .lp-ov-c3 { width: 70px; height: 70px; top: 42%; left: 28%; background: rgba(124,58,237,.04); border: none; border-radius: 50%; pointer-events: none; }
        .lp-ov-dots { position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.025) 1px, transparent 1px); background-size: 24px 24px; pointer-events: none; }

        .lp-ovc {
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2.5rem 2rem;
            transition: transform .8s cubic-bezier(.65,0,.35,1), opacity .6s ease;
        }

        .lp-ovcr { left: 0; transform: translateX(0); opacity: 1; }
        .lp-at-l .lp-ovcr { transform: translateX(-100%); opacity: 0; pointer-events: none; }
        .lp-ovcl { left: 0; transform: translateX(100%); opacity: 0; pointer-events: none; }
        .lp-at-l .lp-ovcl { transform: translateX(0); opacity: 1; pointer-events: auto; }

        .lp-ov-icon { width: 60px; height: 60px; border-radius: 16px; background: rgba(124,58,237,.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.4rem; }
        .lp-ov-icon svg { width: 28px; height: 28px; color: #a78bfa; }
        .lp-ov-h { font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0 0 .45rem; letter-spacing: -.01em; }
        .lp-ov-p { font-size: .82rem; color: rgba(255,255,255,.35); margin: 0 0 1.6rem; line-height: 1.6; max-width: 250px; }

        .lp-ov-btn {
            background: transparent;
            border: 1.5px solid rgba(255,255,255,.18);
            color: rgba(255,255,255,.85);
            padding: .68rem 1.8rem;
            border-radius: 11px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .25s;
            font-family: inherit;
        }

        .lp-ov-btn:hover { border-color: rgba(168,85,247,.4); background: rgba(124,58,237,.06); color: #fff; }

        @media (max-width: 768px) {
            .lp-box { height: auto; min-height: auto; border-radius: 20px; max-width: 440px; }
            .lp-panels { flex-direction: column; }
            .lp-pnl { width: 100%; padding: 2rem 1.5rem; display: none; }
            .lp-pnl.active-mobile { display: flex; }
            .lp-ov { display: none; }
        }
    </style>
</head>
<body>
    <nav class="lp-nav">
        <a href="{{ url('/') }}" class="lp-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            CV<span>BUILDER</span>
        </a>
        <ul class="lp-links">
            <li><a href="{{ url('/#services') }}">Fitur</a></li>
            <li><a href="{{ url('/#templates') }}">Template</a></li>
            <li><a href="{{ url('/#cases') }}">Portfolio</a></li>
            <li><a href="{{ url('/#contact') }}">Kontak</a></li>
        </ul>
        <a href="{{ route('cv-builder.templates') }}" class="lp-btn-nav">Buat CV</a>
    </nav>

    <div class="lp-bg-orb lp-bg-orb-1"></div>
    <div class="lp-bg-orb lp-bg-orb-2"></div>
    <div class="lp-bg-grid"></div>

    <div class="lp-main">
        <div class="lp-box">
            <div class="lp-panels">
                <div class="lp-pnl {{ $currentPanel === 'register' ? 'active-mobile' : '' }}" id="panel-reg">
                    <div class="lp-pnl-in">
                        <h2 class="lp-h">Buat Akun</h2>
                        <p class="lp-sub">Daftar gratis dan mulai bangun karir profesional Anda</p>

                        <form id="regForm" method="POST" action="{{ route('register') }}" novalidate>
                            @csrf
                            <div class="lp-fg">
                                <label for="reg-name">Nama Lengkap</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                    <input type="text" name="name" id="reg-name" class="lp-in @if($triedRegister && $errors->has('name')) lp-err-input @endif" value="{{ $triedRegister ? old('name') : '' }}" placeholder="John Doe" required>
                                </div>
                                <div class="lp-err">@if($triedRegister) @error('name') {{ $message }} @enderror @endif</div>
                            </div>

                            <div class="lp-fg">
                                <label for="reg-email">Email</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                    <input type="email" name="email" id="reg-email" class="lp-in @if($triedRegister && $errors->has('email')) lp-err-input @endif" value="{{ $triedRegister ? old('email') : '' }}" placeholder="nama@email.com" required>
                                </div>
                                <div class="lp-err">@if($triedRegister) @error('email') {{ $message }} @enderror @endif</div>
                            </div>

                            <div class="lp-fg">
                                <label for="reg-pass">Password</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/></svg>
                                    <input type="password" name="password" id="reg-pass" class="lp-in @if($triedRegister && $errors->has('password')) lp-err-input @endif" placeholder="Buat password" required>
                                    <button type="button" class="lp-ptog toggle-pass" aria-label="Tampilkan password" data-target="reg-pass">
                                        <span class="eye-on"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg></span>
                                        <span class="eye-off" style="display:none"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></span>
                                    </button>
                                </div>
                                <div class="lp-err">@if($triedRegister) @error('password') {{ $message }} @enderror @endif</div>
                            </div>

                            <div class="lp-fg">
                                <label for="reg-pass-conf">Konfirmasi Password</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                    <input type="password" name="password_confirmation" id="reg-pass-conf" class="lp-in @if($triedRegister && $errors->has('password_confirmation')) lp-err-input @endif" placeholder="Ulangi password" required>
                                </div>
                                <div class="lp-err">@if($triedRegister) @error('password_confirmation') {{ $message }} @enderror @endif</div>
                            </div>

                            <button type="submit" class="lp-btn" id="regBtn">Daftar Sekarang</button>

                            <div class="lp-btm">
                                Sudah punya akun? <a id="link-to-login" href="{{ route('login') }}">Masuk</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lp-pnl {{ $currentPanel === 'login' ? 'active-mobile' : '' }}" id="panel-log">
                    <div class="lp-pnl-in">
                        <h2 class="lp-h">Masuk</h2>
                        <p class="lp-sub">Selamat datang kembali! Silakan masuk.</p>

                        @if (session('status'))
                            <div class="lp-alert">{{ session('status') }}</div>
                        @endif

                        <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                            @csrf
                            <div class="lp-fg">
                                <label for="log-email">Email</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                    <input type="email" name="email" id="log-email" class="lp-in @if($triedLogin && $errors->has('email')) lp-err-input @endif" value="{{ $triedLogin ? old('email') : '' }}" placeholder="nama@email.com" required>
                                </div>
                                <div class="lp-err">@if($triedLogin) @error('email') {{ $message }} @enderror @endif</div>
                            </div>

                            <div class="lp-fg">
                                <label for="log-pass">Password</label>
                                <div class="lp-iw">
                                    <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/></svg>
                                    <input type="password" name="password" id="log-pass" class="lp-in @if($triedLogin && $errors->has('password')) lp-err-input @endif" placeholder="Masukkan password" required>
                                    <button type="button" class="lp-ptog toggle-pass" aria-label="Tampilkan password" data-target="log-pass">
                                        <span class="eye-on"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg></span>
                                        <span class="eye-off" style="display:none"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></span>
                                    </button>
                                </div>
                                <div class="lp-err">@if($triedLogin) @error('password') {{ $message }} @enderror @endif</div>
                            </div>

                            <label class="lp-ck">
                                <input type="checkbox" name="remember" value="1" @checked($triedLogin && old('remember'))>
                                <span class="lp-ckb"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></span>
                                <span class="lp-ckt">Ingat saya</span>
                            </label>

                            <button type="submit" class="lp-btn" id="logBtn">Masuk ke CV</button>

                            <div class="lp-btm">
                                Belum punya akun? <a id="link-to-register" href="{{ route('register') }}">Daftar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lp-ov {{ $currentPanel === 'login' ? 'lp-at-l' : '' }}" id="lpOv">
                <div class="lp-ov-dots"></div>
                <div class="lp-ov-c1"></div>
                <div class="lp-ov-c2"></div>
                <div class="lp-ov-c3"></div>

                <div class="lp-ovc lp-ovcr">
                    <div class="lp-ov-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    </div>
                    <h2 class="lp-ov-h">Sudah Punya Akun?</h2>
                    <p class="lp-ov-p">Masuk kembali dan lanjutkan pembuatan CV Anda tanpa hambatan.</p>
                    <button type="button" class="lp-ov-btn" id="btnGoLog">Masuk Sekarang</button>
                </div>

                <div class="lp-ovc lp-ovcl">
                    <div class="lp-ov-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                    </div>
                    <h2 class="lp-ov-h">Baru di Sini?</h2>
                    <p class="lp-ov-p">Buat akun baru dan nikmati akses ke 6+ Template Pro kami.</p>
                    <button type="button" class="lp-ov-btn" id="btnGoReg">Daftar Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var lpOv = document.getElementById('lpOv');
            var panelReg = document.getElementById('panel-reg');
            var panelLog = document.getElementById('panel-log');

            function slideToLogin() {
                lpOv.classList.add('lp-at-l');
                if (window.innerWidth <= 768) {
                    panelReg.classList.remove('active-mobile');
                    panelLog.classList.add('active-mobile');
                }
            }

            function slideToRegister() {
                lpOv.classList.remove('lp-at-l');
                if (window.innerWidth <= 768) {
                    panelLog.classList.remove('active-mobile');
                    panelReg.classList.add('active-mobile');
                }
            }

            document.getElementById('btnGoLog').addEventListener('click', slideToLogin);
            document.getElementById('btnGoReg').addEventListener('click', slideToRegister);

            document.getElementById('link-to-login').addEventListener('click', function (e) {
                if (window.innerWidth > 768) {
                    e.preventDefault();
                    slideToLogin();
                }
            });

            document.getElementById('link-to-register').addEventListener('click', function (e) {
                if (window.innerWidth > 768) {
                    e.preventDefault();
                    slideToRegister();
                }
            });

            document.querySelectorAll('.toggle-pass').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = document.getElementById(this.getAttribute('data-target'));
                    if (!target) return;

                    var type = target.getAttribute('type') === 'password' ? 'text' : 'password';
                    target.setAttribute('type', type);

                    this.querySelector('.eye-on').style.display = type === 'text' ? 'none' : 'block';
                    this.querySelector('.eye-off').style.display = type === 'text' ? 'block' : 'none';
                });
            });
        })();
    </script>
</body>
</html>
