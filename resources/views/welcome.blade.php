<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Digital CV Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{font-family:'Inter',system-ui,sans-serif;box-sizing:border-box}
        body{background:#08051a;margin:0;overflow-x:hidden}

        /* --- Background --- */
        @keyframes orbPulse{0%,100%{opacity:.2;transform:scale(1)}50%{opacity:.35;transform:scale(1.06)}}
        @keyframes gridScroll{0%{transform:translate(0,0)}100%{transform:translate(70px,70px)}}
        @keyframes starTwinkle{0%,100%{opacity:0}50%{opacity:.7}}

        /* --- Elements --- */
        @keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
        @keyframes slideScale{from{opacity:0;transform:scale(.9) translateY(16px)}to{opacity:1;transform:scale(1) translateY(0)}}
        @keyframes slideLeft{from{opacity:0;transform:translateX(28px)}to{opacity:1;transform:translateX(0)}}
        @keyframes slideRight{from{opacity:0;transform:translateX(-28px)}to{opacity:1;transform:translateX(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        @keyframes floatSlow{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-14px) rotate(1.5deg)}}
        @keyframes rocketBounce{0%,100%{transform:translateY(0) rotate(-6deg)}50%{transform:translateY(-8px) rotate(6deg)}}
        @keyframes gradientShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
        @keyframes ringPulse{0%{transform:scale(.8);opacity:.6}100%{transform:scale(1.5);opacity:0}}
        @keyframes shimmer{0%{left:-100%}100%{left:200%}}
        @keyframes badgePulse{0%,100%{box-shadow:0 0 0 0 rgba(168,85,247,.25)}50%{box-shadow:0 0 0 6px rgba(168,85,247,0)}}
        @keyframes checkPop{from{opacity:0;transform:scale(0)}to{opacity:1;transform:scale(1)}}
        @keyframes borderGlow{0%,100%{border-color:rgba(168,85,247,.08)}50%{border-color:rgba(168,85,247,.18)}}

        .fu{animation:fadeUp .7s cubic-bezier(.16,1,.3,1) both}
        .ss{animation:slideScale .7s cubic-bezier(.16,1,.3,1) both}
        .sl{animation:slideLeft .7s cubic-bezier(.16,1,.3,1) both}
        .sr{animation:slideRight .7s cubic-bezier(.16,1,.3,1) both}
        .fl{animation:float 5s ease-in-out infinite}
        .fs{animation:floatSlow 7s ease-in-out infinite}
        .d1{animation-delay:.1s}.d2{animation-delay:.2s}.d3{animation-delay:.3s}
        .d4{animation-delay:.4s}.d5{animation-delay:.5s}.d6{animation-delay:.6s}
        .d7{animation-delay:.7s}.d8{animation-delay:.8s}.d9{animation-delay:.9s}
        .d10{animation-delay:1s}.d11{animation-delay:1.1s}.d12{animation-delay:1.2s}
        .d13{animation-delay:1.3s}.d14{animation-delay:1.4s}

        .grad-text{
            background:linear-gradient(135deg,#c084fc,#a855f7,#7c3aed,#c084fc);
            background-size:300% 300%;
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
            animation:gradientShift 8s ease infinite;
        }
        .glass{background:rgba(255,255,255,.03);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.06)}
        .glass-lg{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.08)}

        .btn-main{position:relative;overflow:hidden;background:linear-gradient(135deg,#7c3aed,#9333ea);transition:all .25s}
        .btn-main::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,#9333ea,#7c3aed);opacity:0;transition:opacity .25s}
        .btn-main::after{content:'';position:absolute;top:0;left:-100%;width:50%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);animation:shimmer 4s ease-in-out infinite}
        .btn-main:hover::before{opacity:1}
        .btn-main:hover{transform:translateY(-2px);box-shadow:0 0 30px rgba(147,51,234,.35)}
        .btn-main:active{transform:translateY(0) scale(.98)}
        .btn-main>*{position:relative;z-index:1}

        .btn-outline{transition:all .2s;border:1px solid rgba(168,85,247,.2)}
        .btn-outline:hover{border-color:rgba(168,85,247,.4);background:rgba(168,85,247,.06);color:#d8b4fe;transform:translateY(-1px)}

        .feature-block{position:relative;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);animation:borderGlow 5s ease-in-out infinite}
        .feature-block::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(168,85,247,.5),transparent);opacity:0;transition:opacity .3s}
        .feature-block:hover::before{opacity:1}
        .feature-block:hover{transform:translateY(-4px);box-shadow:0 20px 40px -10px rgba(147,51,234,.15);border-color:rgba(168,85,247,.2)}

        .step-card{transition:all .25s;position:relative;overflow:hidden}
        .step-card::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% 0%,rgba(147,51,234,.06),transparent 60%);opacity:0;transition:opacity .3s}
        .step-card:hover::before{opacity:1}
        .step-card:hover{transform:translateY(-4px);border-color:rgba(168,85,247,.18);box-shadow:0 12px 32px -8px rgba(147,51,234,.12)}

        .stat-num{background:linear-gradient(135deg,#e9d5ff,#c084fc,#a78bfa);background-size:200% 200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:gradientShift 6s ease infinite}

        .check-item{opacity:0;animation:fadeUp .5s cubic-bezier(.16,1,.3,1) both}
        .price-row{opacity:0;animation:slideRight .4s cubic-bezier(.16,1,.3,1) both}
        .avatar-item{opacity:0;animation:slideScale .4s cubic-bezier(.34,1.56,.64,1) both}

        ::-webkit-scrollbar{width:5px}
        ::-webkit-scrollbar-track{background:#08051a}
        ::-webkit-scrollbar-thumb{background:rgba(124,58,237,.25);border-radius:3px}
    </style>
</head>
<body>

    {{-- ===== BACKGROUND (ringan) ===== --}}
    <div style="position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;">
        {{-- 3 aurora blobs — hanya opacity pulse, tidak gerak --}}
        <div style="position:absolute;top:-15%;left:-10%;width:60vw;height:60vw;max-width:700px;max-height:700px;border-radius:50%;background:radial-gradient(ellipse,rgba(124,58,237,.1),transparent 65%);filter:blur(60px);animation:orbPulse 12s ease-in-out infinite;"></div>
        <div style="position:absolute;top:15%;right:-12%;width:50vw;height:50vw;max-width:600px;max-height:600px;border-radius:50%;background:radial-gradient(ellipse,rgba(168,85,247,.08),transparent 65%);filter:blur(50px);animation:orbPulse 15s ease-in-out infinite 3s;"></div>
        <div style="position:absolute;bottom:-10%;left:30%;width:40vw;height:40vw;max-width:500px;max-height:500px;border-radius:50%;background:radial-gradient(ellipse,rgba(192,132,252,.06),transparent 60%);filter:blur(50px);animation:orbPulse 18s ease-in-out infinite 6s;"></div>

        {{-- Grid — scroll pelan --}}
        <div style="position:absolute;inset:0;opacity:.02;background-image:linear-gradient(rgba(255,255,255,.5) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.5) 1px,transparent 1px);background-size:70px 70px;animation:gridScroll 30s linear infinite;"></div>

        {{-- 20 bintang (dari 40) --}}
        @for($i = 0; $i < 20; $i++)
            <div style="position:absolute;width:{{ rand(1,2) }}px;height:{{ rand(1,2) }}px;border-radius:50%;background:rgba(255,255,255,.5);top:{{ rand(0,100) }}%;left:{{ rand(0,100) }}%;animation:starTwinkle {{ rand(4,8) }}s ease-in-out infinite {{ rand(0,5) }}s;"></div>
        @endfor
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav style="position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,5,26,.8);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.04);animation:fadeUp .5s ease both;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6" style="height:60px;display:flex;align-items:center;justify-content:space-between;">
            <a href="/" style="font-size:1.1rem;font-weight:800;color:#fff;text-decoration:none;letter-spacing:-.03em;display:flex;align-items:center;gap:9px;">
                <span class="fl" style="width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;box-shadow:0 0 18px rgba(124,58,237,.3);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                </span>
                CVBuilder
            </a>
            <div style="display:flex;align-items:center;gap:5px;">
                @auth
                    <a href="{{ route('cvs.index') }}" class="btn-outline fu d1" style="display:flex;align-items:center;gap:5px;padding:7px 14px;border-radius:8px;font-size:.8rem;font-weight:500;color:rgba(255,255,255,.55);text-decoration:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>
                    @if(auth()->user()->role?->slug === 'admin')
                        <a href="{{ route('admin.templates.index') }}" class="btn-outline fu d2" style="padding:7px 14px;border-radius:8px;font-size:.8rem;font-weight:500;color:rgba(255,255,255,.55);text-decoration:none;">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf
                        <button type="submit" class="fu d3" style="padding:7px 14px;border-radius:8px;font-size:.8rem;font-weight:500;color:rgba(255,255,255,.3);background:transparent;border:none;cursor:pointer;transition:color .15s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='rgba(255,255,255,.3)'">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="fu d1" style="padding:7px 14px;border-radius:8px;font-size:.8rem;font-weight:500;color:rgba(255,255,255,.55);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.55)'">Login</a>
                    <a href="{{ route('register') }}" class="btn-main fu d2" style="display:inline-flex;align-items:center;gap:5px;padding:8px 18px;border-radius:8px;font-size:.8rem;font-weight:600;color:#fff;text-decoration:none;">
                        <span style="position:relative;z-index:1;">Get Started</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main style="position:relative;z-index:1;">

        {{-- ===== HERO ===== --}}
        <section style="padding:8rem 1rem 3.5rem;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center" style="max-width:680px;margin:0 auto;">

                    {{-- Pill --}}
                    <div class="fu" style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;margin-bottom:24px;background:rgba(147,51,234,.1);border:1px solid rgba(147,51,234,.2);animation:badgePulse 3s ease-in-out infinite;">
                        <span style="position:relative;width:7px;height:7px;border-radius:50%;background:#a855f7;">
                            <span style="position:absolute;inset:-3px;border-radius:50%;border:1.5px solid rgba(168,85,247,.4);animation:ringPulse 2.5s ease-out infinite;"></span>
                        </span>
                        <span style="font-size:.72rem;font-weight:600;color:#d8b4fe;letter-spacing:.04em;">Digital Resume Platform</span>
                    </div>

                    {{-- Heading --}}
                    <h1 class="fu d1" style="font-size:clamp(2.4rem,5.5vw,3.8rem);font-weight:900;color:#fff;letter-spacing:-.04em;line-height:1.08;margin:0;">
                        Learn & Build<br><span class="grad-text">Your Future.</span>
                    </h1>

                    {{-- Subtitle --}}
                    <p class="fu d2" style="margin-top:18px;font-size:1.05rem;color:rgba(255,255,255,.35);line-height:1.75;max-width:520px;margin-left:auto;margin-right:auto;">
                        Buat CV digital profesional dari berbagai template. Simpan sebagai draft atau publish langsung untuk dibagikan ke recruiter.
                    </p>

                    {{-- CTAs --}}
                    <div class="fu d3 flex justify-center flex-wrap" style="margin-top:32px;gap:12px;">
                        @auth
                            <a href="{{ route('cv-builder.templates') }}" class="btn-main" style="display:inline-flex;align-items:center;gap:9px;padding:14px 34px;border-radius:12px;font-size:.9rem;font-weight:700;color:#fff;text-decoration:none;box-shadow:0 4px 20px -4px rgba(147,51,234,.4);">
                                <span style="position:relative;z-index:1;display:flex;align-items:center;gap:9px;">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Start Build CV
                                </span>
                            </a>
                            <a href="{{ route('cvs.index') }}" class="btn-outline" style="display:inline-flex;align-items:center;gap:8px;padding:14px 34px;border-radius:12px;font-size:.9rem;font-weight:600;color:rgba(255,255,255,.55);text-decoration:none;">My CVs</a>
                        @else
                            <a href="{{ route('register') }}" class="btn-main" style="display:inline-flex;align-items:center;gap:9px;padding:14px 34px;border-radius:12px;font-size:.9rem;font-weight:700;color:#fff;text-decoration:none;box-shadow:0 4px 20px -4px rgba(147,51,234,.4);">
                                <span style="position:relative;z-index:1;display:flex;align-items:center;gap:9px;">
                                    Enroll Now
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                                </span>
                            </a>
                            <a href="{{ route('login') }}" class="btn-outline" style="display:inline-flex;align-items:center;gap:8px;padding:14px 34px;border-radius:12px;font-size:.9rem;font-weight:600;color:rgba(255,255,255,.55);text-decoration:none;">Login</a>
                        @endauth
                    </div>

                    {{-- Stats --}}
                    <div class="fu d5 flex justify-center flex-wrap" style="margin-top:44px;gap:0;">
                        @foreach([['12+','Templates'],['500+','Users'],['100%','Free']] as $i => $s)
                            <div class="fu d{{ $i + 6 }}" style="text-align:center;padding:0 {{ $i < 2 ? '24' : '0' }}px;{{ $i < 2 ? 'border-right:1px solid rgba(255,255,255,.06);margin-right:24px;' : '' }}">
                                <div class="stat-num" style="font-size:1.3rem;font-weight:800;">{{ $s[0] }}</div>
                                <div style="font-size:.72rem;color:rgba(255,255,255,.25);margin-top:2px;">{{ $s[1] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== TWO BLOCKS ===== --}}
        <section style="padding:1.5rem 1rem 4rem;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="grid grid-cols-1 md:grid-cols-2" style="gap:18px;">

                    {{-- LEFT --}}
                    <div class="feature-block glass-lg sl d7" style="border-radius:22px;padding:2.2rem;position:relative;">
                        <div style="position:absolute;top:-24px;right:-24px;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.12),transparent 70%);animation:orbPulse 10s ease-in-out infinite;pointer-events:none;"></div>

                        <div style="position:relative;z-index:1;">
                            <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,rgba(124,58,237,.18),rgba(168,85,247,.12));display:flex;align-items:center;justify-content:center;margin-bottom:22px;animation:rocketBounce 3.5s ease-in-out infinite;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                                </svg>
                            </div>

                            <h2 class="sr d8" style="font-size:clamp(1.3rem,2.8vw,1.65rem);font-weight:800;color:#fff;letter-spacing:-.03em;margin:0 0 10px;line-height:1.2;">
                                Give You<br><span class="grad-text">100% Reliability</span>
                            </h2>
                            <p class="fu d9" style="font-size:.875rem;color:rgba(255,255,255,.35);line-height:1.75;margin:0 0 22px;">
                                Data kamu aman dan tersimpan. Akses CV kapan saja, di mana saja.
                            </p>

                            <div style="display:flex;flex-direction:column;gap:11px;">
                                @foreach(['Auto-save setiap perubahan data','Cloud storage — tidak pernah hilang','Akses cepat dari perangkat mana saja','Tanpa batasan jumlah CV'] as $j => $item)
                                    <div class="check-item flex items-center d{{ $j + 10 }}" style="gap:10px;">
                                        <div style="width:20px;height:20px;border-radius:5px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation:checkPop .4s cubic-bezier(.34,1.56,.64,1) {{ .15 + $j * .1 }}s both;"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                        <span style="font-size:.8rem;color:rgba(255,255,255,.45);font-weight:500;">{{ $item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="feature-block glass-lg sr d8" style="border-radius:22px;padding:2.2rem;position:relative;">
                        <div style="position:absolute;top:-30px;left:-30px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.07),transparent 70%);animation:orbPulse 11s ease-in-out infinite 2s;pointer-events:none;"></div>

                        <div style="position:relative;z-index:1;">
                            <div class="fs" style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(251,191,36,.08));display:flex;align-items:center;justify-content:center;margin-bottom:22px;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                                </svg>
                            </div>

                            <div class="fu d9" style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:100px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.18);margin-bottom:14px;animation:badgePulse 4s ease-in-out infinite 1s;">
                                <span style="font-size:1.15rem;font-weight:900;color:#fbbf24;">100%</span>
                                <span style="font-size:.65rem;font-weight:700;color:#fde68a;text-transform:uppercase;letter-spacing:.05em;">Free</span>
                            </div>

                            <h2 class="sl d10" style="font-size:clamp(1.3rem,2.8vw,1.65rem);font-weight:800;color:#fff;letter-spacing:-.03em;margin:0 0 10px;line-height:1.2;">
                                Zero Cost.<br><span style="color:rgba(255,255,255,.4);font-weight:600;">Full Features.</span>
                            </h2>
                            <p class="fu d10" style="font-size:.875rem;color:rgba(255,255,255,.35);line-height:1.75;margin:0 0:22px;">
                                Semua fitur premium gratis. Tanpa trial, tanpa hidden fee.
                            </p>

                            <div style="display:flex;flex-direction:column;gap:7px;margin-top:22px;">
                                @foreach([['Template Gallery'],['Visual Editor'],['Publish & Share Link'],['Unlimited CV']] as $j => $item)
                                    <div class="price-row flex items-center justify-between d{{ $j + 11 }}" style="padding:10px 13px;border-radius:9px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.04);transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='rgba(255,255,255,.02)'">
                                        <span style="font-size:.8rem;color:rgba(255,255,255,.45);font-weight:500;">{{ $item[0] }}</span>
                                        <span style="font-size:.8rem;font-weight:700;color:#34d399;">$0</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== HOW IT WORKS ===== --}}
        <section style="padding:.5rem 1rem 4rem;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center fu d9" style="margin-bottom:2.5rem;">
                    <span style="display:inline-block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:#a78bfa;margin-bottom:8px;">How it works</span>
                    <h2 style="font-size:clamp(1.4rem,2.8vw,1.85rem);font-weight:800;color:#fff;letter-spacing:-.03em;margin:0;">Tiga langkah mudah.</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3" style="gap:14px;">
                    @foreach([
                        ['01','Pilih Template','Jelajahi koleksi template profesional dan pilih yang cocok.','🚀'],
                        ['02','Isi Datamu','Masukkan pengalaman, pendidikan, dan skill dengan editor intuitif.','✏️'],
                        ['03','Publish & Share','Dapatkan link publik untuk dibagikan ke recruiter.','🚀'],
                    ] as $i => $step)
                        <div class="step-card glass fu d{{ $i + 10 }}" style="border-radius:16px;padding:26px 22px;text-align:center;">
                            <div class="fl" style="font-size:2rem;margin-bottom:12px;animation-delay:{{ $i * .6 }}s;">{{ $step[3] }}</div>
                            <div style="font-size:.65rem;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.15em;margin-bottom:7px;">Step {{ $step[0] }}</div>
                            <h3 style="font-size:.95rem;font-weight:700;color:#fff;margin:0 0 8px;">{{ $step[1] }}</h3>
                            <p style="font-size:.8rem;color:rgba(255,255,255,.3);line-height:1.65;margin:0;">{{ $step[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== TRUSTED BY ===== --}}
        <section style="padding:.5rem 1rem 3.5rem;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="glass-lg fu d12" style="border-radius:22px;padding:3rem 2rem;text-align:center;position:relative;overflow:hidden;animation:borderGlow 6s ease-in-out infinite;">
                    <div style="position:absolute;top:-60px;left:50%;transform:translateX(-50%);width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(147,51,234,.08),transparent 70%);animation:orbPulse 12s ease-in-out infinite;pointer-events:none;"></div>

                    <div style="position:relative;z-index:1;">
                        <span class="fu d12" style="display:inline-block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:rgba(255,255,255,.15);margin-bottom:18px;">Trusted by Innovators</span>

                        <div class="flex justify-center flex-wrap" style="gap:7px;margin-bottom:18px;">
                            @php
                                $g = ['rgba(124,58,237,.25),rgba(168,85,247,.1)','rgba(168,85,247,.2),rgba(192,132,252,.08)','rgba(192,132,252,.18),rgba(124,58,237,.08)','rgba(147,51,234,.25),rgba(124,58,237,.08)','rgba(124,58,237,.18),rgba(168,85,247,.15)','rgba(168,85,247,.25),rgba(192,132,252,.08)','rgba(192,132,252,.2),rgba(147,51,234,.08)','rgba(147,51,234,.18),rgba(168,85,247,.12)','rgba(124,58,237,.22),rgba(192,132,252,.08)','rgba(168,85,247,.18),rgba(124,58,237,.12)','rgba(192,132,252,.25),rgba(168,85,247,.08)','rgba(147,51,234,.2),rgba(192,132,252,.12)'];
                            @endphp
                            @foreach(range(0,11) as $j)
                                <div class="avatar-item d{{ min($j + 13, 14) }}" style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,{{ $g[$j] }});border:2px solid rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#c4b5fd;cursor:default;transition:transform .2s cubic-bezier(.34,1.56,.64,1);" onmouseover="this.style.transform='translateY(-3px) scale(1.1)'" onmouseout="this.style.transform='translateY(0) scale(1)'">
                                    {{ chr(65 + $j) }}
                                </div>
                            @endforeach
                        </div>

                        <p class="fu d13" style="font-size:.95rem;color:rgba(255,255,255,.4);max-width:380px;margin:0 auto 24px;line-height:1.65;">
                            Bergabung bersama ratusan profesional yang sudah mempercayakan karir mereka ke CVBuilder.
                        </p>

                        @auth
                            <a href="{{ route('cv-builder.templates') }}" class="btn-main fu d14" style="display:inline-flex;align-items:center;gap:9px;padding:13px 30px;border-radius:12px;font-size:.85rem;font-weight:700;color:#fff;text-decoration:none;">
                                <span style="position:relative;z-index:1;display:flex;align-items:center;gap:9px;">Mulai Sekarang <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg></span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-main fu d14" style="display:inline-flex;align-items:center;gap:9px;padding:13px 30px;border-radius:12px;font-size:.85rem;font-weight:700;color:#fff;text-decoration:none;">
                                <span style="position:relative;z-index:1;display:flex;align-items:center;gap:9px;">Get Started Free <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg></span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer style="border-top:1px solid rgba(255,255,255,.03);padding:1.25rem 1rem;position:relative;z-index:1;animation:fadeUp .5s ease both 1.4s;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between" style="gap:6px;">
            <span style="font-size:.72rem;color:rgba(255,255,255,.1);">&copy; {{ date('Y') }} CVBuilder. All rights reserved.</span>
            <span style="font-size:.72rem;color:rgba(255,255,255,.05);">Built with Laravel & Tailwind CSS</span>
        </div>
    </footer>

</body>
</html>