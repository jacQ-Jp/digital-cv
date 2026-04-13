@extends('layouts.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.lp-auth{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:#08051a;z-index:9999;padding:1.25rem;font-family:'Inter',system-ui,-apple-system,sans-serif;overflow:hidden}

/* Background — fully static, no animation */
.lp-bg-orb{position:absolute;border-radius:50%;pointer-events:none}
.lp-bg-orb-1{width:450px;height:450px;top:-14%;right:-10%;background:radial-gradient(circle,rgba(124,58,237,.1),transparent 60%);filter:blur(60px)}
.lp-bg-orb-2{width:380px;height:380px;bottom:-12%;left:-8%;background:radial-gradient(circle,rgba(168,85,247,.07),transparent 60%);filter:blur(60px)}
.lp-bg-grid{position:absolute;inset:0;opacity:.015;background-image:linear-gradient(rgba(255,255,255,.5) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.5) 1px,transparent 1px);background-size:70px 70px;pointer-events:none}

/* Card — gentle entry */
.lp-box{position:relative;width:100%;max-width:900px;height:600px;background:rgba(255,255,255,.035);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.06);border-radius:28px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.3);animation:lpEntry .8s cubic-bezier(.16,1,.3,1) both}
@keyframes lpEntry{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:none}}

/* Panels */
.lp-panels{display:flex;height:100%;position:relative;z-index:1}
.lp-pnl{width:50%;display:flex;align-items:center;justify-content:center;padding:2.5rem 2rem}
.lp-pnl-in{width:100%;max-width:310px}

/* Typography */
.lp-h{font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 .25rem;letter-spacing:-.02em}
.lp-sub{font-size:.82rem;color:rgba(255,255,255,.3);margin:0 0 1.5rem;line-height:1.55}

/* Inputs */
.lp-fg{margin-bottom:.9rem}
.lp-fg label{display:block;font-size:.68rem;font-weight:600;color:rgba(255,255,255,.35);margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.06em;transition:color .3s}
.lp-fg:focus-within label{color:#a78bfa}
.lp-iw{position:relative;display:flex;align-items:center}
.lp-ic{position:absolute;left:12px;color:rgba(255,255,255,.15);width:16px;height:16px;transition:color .3s;pointer-events:none;flex-shrink:0}
.lp-fg:focus-within .lp-ic{color:#a78bfa}
.lp-in{width:100%;padding:.62rem .75rem .62rem 2.5rem;border:1.5px solid rgba(255,255,255,.07);border-radius:11px;font-size:.85rem;color:#fff;background:rgba(255,255,255,.035);outline:none;transition:border-color .3s,box-shadow .3s;font-family:inherit}
.lp-in::placeholder{color:rgba(255,255,255,.18)}
.lp-in:focus{border-color:rgba(168,85,247,.4);box-shadow:0 0 0 3px rgba(168,85,247,.08)}
.lp-in.lp-err-input{border-color:rgba(239,68,68,.4);box-shadow:0 0 0 3px rgba(239,68,68,.06)}
.lp-in.lp-err-input:focus{border-color:rgba(239,68,68,.4);box-shadow:0 0 0 3px rgba(239,68,68,.06)}

/* Password toggle */
.lp-ptog{position:absolute;right:11px;background:none;border:none;color:rgba(255,255,255,.18);cursor:pointer;padding:4px;display:flex;align-items:center;transition:color .2s;outline:none}
.lp-ptog:hover{color:rgba(255,255,255,.45)}
.lp-ptog svg{width:16px;height:16px;display:block}

/* Error */
.lp-err{font-size:.75rem;color:#f87171;margin-top:.25rem;padding-left:2px}

/* Button — no shimmer, clean */
.lp-btn{width:100%;padding:.72rem;border:none;border-radius:11px;background:linear-gradient(135deg,#7c3aed,#9333ea);color:#fff;font-size:.875rem;font-weight:600;cursor:pointer;position:relative;overflow:hidden;transition:box-shadow .3s,transform .15s;font-family:inherit;outline:none}
.lp-btn:hover{box-shadow:0 6px 24px rgba(124,58,237,.3);transform:translateY(-1px)}
.lp-btn:active{transform:translateY(0) scale(.985)}
.lp-btn-inner{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;gap:.4rem}
.lp-spin{display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:lpSp .6s linear infinite;position:absolute}
@keyframes lpSp{to{transform:rotate(360deg)}}
.lp-btn.lp-loading .lp-btn-inner>span,.lp-btn.lp-loading .lp-btn-inner>svg{opacity:0}
.lp-btn.lp-loading .lp-spin{display:block}
.lp-btn.lp-loading{pointer-events:none;opacity:.8}

/* Bottom link */
.lp-btm{text-align:center;margin-top:1.1rem;font-size:.82rem;color:rgba(255,255,255,.25)}
.lp-btm a{color:#a78bfa;text-decoration:none;font-weight:600;transition:color .2s}
.lp-btm a:hover{color:#c4b5fd}

/* Overlay — slower, gentler slide */
.lp-ov{position:absolute;top:0;left:50%;width:50%;height:100%;background:linear-gradient(135deg,#0c0518 0%,#140d22 100%);transition:left .8s cubic-bezier(.65,0,.35,1);z-index:10;overflow:hidden}
.lp-ov.lp-at-l{left:0}

.lp-ov-c1,.lp-ov-c2{position:absolute;border-radius:50%;border:1px solid rgba(255,255,255,.04);pointer-events:none}
.lp-ov-c1{width:180px;height:180px;top:-30px;right:-50px}
.lp-ov-c2{width:120px;height:120px;bottom:-25px;left:-30px}
.lp-ov-c3{width:70px;height:70px;top:42%;left:28%;background:rgba(124,58,237,.04);border:none;border-radius:50%;pointer-events:none}
.lp-ov-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.025) 1px,transparent 1px);background-size:24px 24px;pointer-events:none}

/* Overlay content — smoother transition */
.lp-ovc{position:absolute;top:0;width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2.5rem 2rem;transition:transform .8s cubic-bezier(.65,0,.35,1),opacity .6s ease}
.lp-ovcr{left:0;transform:translateX(0);opacity:1}
.lp-at-l .lp-ovcr{transform:translateX(-100%);opacity:0;pointer-events:none}
.lp-ovcl{left:0;transform:translateX(100%);opacity:0;pointer-events:none}
.lp-at-l .lp-ovcl{transform:translateX(0);opacity:1;pointer-events:auto}

.lp-ov-icon{width:60px;height:60px;border-radius:16px;background:rgba(124,58,237,.1);display:flex;align-items:center;justify-content:center;margin-bottom:1.4rem}
.lp-ov-icon svg{width:28px;height:28px;color:#a78bfa}
.lp-ov-h{font-size:1.3rem;font-weight:800;color:#fff;margin:0 0 .45rem;letter-spacing:-.01em}
.lp-ov-p{font-size:.82rem;color:rgba(255,255,255,.35);margin:0 0 1.6rem;line-height:1.6;max-width:250px}

.lp-ov-btn{background:transparent;border:1.5px solid rgba(255,255,255,.18);color:rgba(255,255,255,.85);padding:.68rem 1.8rem;border-radius:11px;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .25s;font-family:inherit;outline:none}
.lp-ov-btn:hover{border-color:rgba(168,85,247,.4);background:rgba(124,58,237,.06);color:#fff}
.lp-ov-btn:active{transform:scale(.97)}

/* Responsive */
@media(max-width:768px){
    .lp-box{height:auto;border-radius:20px;max-width:440px}
    .lp-panels{flex-direction:column}
    .lp-pnl{width:100%;padding:2rem 1.5rem}
    .lp-pnl.lp-hide-mobile{display:none}
    .lp-ov{display:none}
}
@media(prefers-reduced-motion:reduce){
    .lp-auth,.lp-auth *,.lp-auth *::before,.lp-auth *::after{animation-duration:.01ms!important;transition-duration:.01ms!important}
}
</style>

<div class="lp-auth">
    <div class="lp-bg-orb lp-bg-orb-1"></div>
    <div class="lp-bg-orb lp-bg-orb-2"></div>
    <div class="lp-bg-grid"></div>

    <div class="lp-box" id="lpBox">

        <div class="lp-panels">

            <!-- LEFT: Login (hidden by overlay) -->
            <div class="lp-pnl lp-hide-mobile">
                <div class="lp-pnl-in">
                    <h2 class="lp-h">Masuk</h2>
                    <p class="lp-sub">Masuk ke akun Anda untuk melanjutkan</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="lp-fg"><label>Email</label><div class="lp-iw"><svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg><input type="email" name="email" class="lp-in" placeholder="nama@email.com" required></div></div>
                        <div class="lp-fg"><label>Password</label><div class="lp-iw"><svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/></svg><input type="password" name="password" class="lp-in" placeholder="Masukkan password" required></div></div>
                        <label class="lp-ck" style="display:flex;align-items:center;gap:.5rem;cursor:pointer;margin-bottom:1.4rem;user-select:none"><input type="checkbox" name="remember" value="1" style="position:absolute;opacity:0;width:0;height:0"><span class="lp-ckb"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></span><span class="lp-ckt">Ingat saya</span></label>
                        <button type="submit" class="lp-btn"><span class="lp-btn-inner"><span>Masuk</span></span></button>
                        <div class="lp-btm">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></div>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Register (active) -->
            <div class="lp-pnl">
                <div class="lp-pnl-in">
                    <h2 class="lp-h">Buat Akun</h2>
                    <p class="lp-sub">Daftar untuk memulai perjalanan Anda</p>

                    <form method="POST" action="{{ route('register') }}" id="lpForm">
                        @csrf

                        <div class="lp-fg">
                            <label for="name">Nama</label>
                            <div class="lp-iw">
                                <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                <input name="name" id="name" class="lp-in {{ $errors->has('name') ? 'lp-err-input' : '' }}" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus>
                            </div>
                            @error('name')<div class="lp-err">{{ $message }}</div>@enderror
                        </div>

                        <div class="lp-fg">
                            <label for="email">Email</label>
                            <div class="lp-iw">
                                <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <input type="email" name="email" id="email" class="lp-in {{ $errors->has('email') ? 'lp-err-input' : '' }}" value="{{ old('email') }}" placeholder="nama@email.com" required>
                            </div>
                            @error('email')<div class="lp-err">{{ $message }}</div>@enderror
                        </div>

                        <div class="lp-fg">
                            <label for="password">Password</label>
                            <div class="lp-iw">
                                <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/></svg>
                                <input type="password" name="password" id="lpPass" class="lp-in {{ $errors->has('password') ? 'lp-err-input' : '' }}" placeholder="Buat password" required>
                                <button type="button" class="lp-ptog" id="lpTog" aria-label="Tampilkan password">
                                    <span id="lpEyeOn"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg></span>
                                    <span id="lpEyeOff" style="display:none"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></span>
                                </button>
                            </div>
                            @error('password')<div class="lp-err">{{ $message }}</div>@enderror
                        </div>

                        <div class="lp-fg">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="lp-iw">
                                <svg class="lp-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="lp-in" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <button type="submit" class="lp-btn" id="lpBtn" style="margin-top:.25rem">
                            <span class="lp-btn-inner">
                                <span>Buat Akun</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                <span class="lp-spin"></span>
                            </span>
                        </button>

                        <div class="lp-btm">
                            Sudah punya akun? <a href="{{ route('login') }}" id="lpLinkLog">Masuk</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Overlay (starts left = register mode) -->
        <div class="lp-ov lp-at-l" id="lpOv">
            <div class="lp-ov-dots"></div>
            <div class="lp-ov-c1"></div>
            <div class="lp-ov-c2"></div>
            <div class="lp-ov-c3"></div>

            <div class="lp-ovc lp-ovcr">
                <div class="lp-ov-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                </div>
                <h2 class="lp-ov-h">Baru di Sini?</h2>
                <p class="lp-ov-p">Buat akun baru dan nikmati semua fitur yang tersedia untuk Anda</p>
                <button type="button" class="lp-ov-btn" id="lpGoReg">Daftar Sekarang</button>
            </div>

            <div class="lp-ovc lp-ovcl">
                <div class="lp-ov-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <h2 class="lp-ov-h">Sudah Punya Akun?</h2>
                <p class="lp-ov-p">Masuk kembali dan lanjutkan aktivitas Anda tanpa hambatan</p>
                <button type="button" class="lp-ov-btn" id="lpGoLog">Masuk Sekarang</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    if(document.querySelector('.lp-err'))document.getElementById('lpBox').style.animation='none';
    if(sessionStorage.getItem('lp-smooth')==='1'){sessionStorage.removeItem('lp-smooth');document.getElementById('lpBox').style.animation='none'}
    document.documentElement.style.backgroundColor='#08051a';
    document.body.style.backgroundColor='#08051a';
    document.body.style.margin='0';
    document.body.style.minHeight='100vh';

    var tog=document.getElementById('lpTog'),inp=document.getElementById('lpPass'),on=document.getElementById('lpEyeOn'),off=document.getElementById('lpEyeOff');
    tog.addEventListener('click',function(){var p=inp.type==='password';inp.type=p?'text':'password';on.style.display=p?'none':'block';off.style.display=p?'block':'none'});

    var form=document.getElementById('lpForm'),btn=document.getElementById('lpBtn');
    form.addEventListener('submit',function(){btn.classList.add('lp-loading')});

    var ov=document.getElementById('lpOv'),logUrl='{{ route("login") }}';
    function slideToLogin(){sessionStorage.setItem('lp-smooth','1');ov.classList.remove('lp-at-l');setTimeout(function(){window.location.href=logUrl},800)}
    document.getElementById('lpGoLog').addEventListener('click',slideToLogin);
    document.getElementById('lpLinkLog').addEventListener('click',function(e){e.preventDefault();slideToLogin()});
})();
</script>
@endsection