<?php // index.php ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SQL Injection Lab — ID-Networkers</title>
<style>
/* ── RESET & BASE ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:15px;scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:#080b13;color:#dde4ef;min-height:100vh;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}

/* ── CSS VARIABLES ── */
:root{
  --bg:#080b13;
  --surface:#0e1420;
  --card:#111827;
  --elevated:#161e2e;
  --border:#1d2b3a;
  --border2:#263446;
  --red:#e63946;
  --red-bg:rgba(230,57,70,.1);
  --red-bdr:rgba(230,57,70,.2);
  --green:#22c55e;
  --green-bg:rgba(34,197,94,.08);
  --green-bdr:rgba(34,197,94,.2);
  --orange:#f59e0b;
  --orange-bg:rgba(245,158,11,.08);
  --orange-bdr:rgba(245,158,11,.2);
  --blue:#38bdf8;
  --t1:#dde4ef;
  --t2:#7b8fa8;
  --t3:#3d5168;
  --mono:'Courier New',monospace;
  --r:8px;--r2:12px
}

/* ── NAVBAR ── */
.nav{
  position:sticky;top:0;z-index:100;
  background:rgba(8,11,19,.96);
  backdrop-filter:blur(16px);
  border-bottom:1px solid var(--border);
  height:60px;display:flex;align-items:center;
  padding:0 40px;gap:32px
}
.nav-logo img{height:28px;display:block}
.nav-menu{display:flex;align-items:center;gap:2px;margin:0 auto}
.nav-menu a{
  font-size:.8rem;font-weight:600;letter-spacing:.02em;
  color:var(--t2);padding:6px 16px;border-radius:6px;
  transition:all .15s
}
.nav-menu a:hover{color:var(--t1);background:var(--elevated)}
.nav-menu a.on{color:#fff;background:var(--red)}
.nav-pill{
  font-size:.65rem;font-weight:700;letter-spacing:.12em;
  text-transform:uppercase;color:var(--red);
  border:1px solid var(--red-bdr);border-radius:20px;
  padding:4px 12px;white-space:nowrap
}

/* ── HERO ── */
.hero{
  background:var(--surface);
  border-bottom:1px solid var(--border)
}
.hero-inner{
  max-width:1160px;margin:0 auto;
  padding:64px 40px 56px;
  display:grid;
  grid-template-columns:1fr 200px;
  gap:56px;align-items:center;
  position:relative
}
.hero-inner::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 50% 100% at 0 50%,rgba(230,57,70,.05),transparent 65%);
  pointer-events:none
}
.hero-eye{
  display:inline-flex;align-items:center;gap:8px;
  font-size:.66rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;
  color:var(--red);background:var(--red-bg);border:1px solid var(--red-bdr);
  padding:4px 12px;border-radius:20px;margin-bottom:20px
}
.hero-eye i{
  width:6px;height:6px;border-radius:50%;background:var(--red);
  animation:blink 2s infinite;flex-shrink:0
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.hero h1{
  font-size:2.6rem;font-weight:800;line-height:1.15;
  letter-spacing:-.025em;color:var(--t1);margin-bottom:16px
}
.hero h1 b{color:var(--red);font-weight:800}
.hero-sub{
  font-size:.9rem;color:var(--t2);
  max-width:500px;line-height:1.8;margin-bottom:24px
}
.hero-note{
  display:inline-flex;align-items:center;gap:10px;
  font-size:.72rem;font-family:var(--mono);color:var(--t3);
  border:1px solid var(--border);border-radius:var(--r);
  padding:8px 16px;background:var(--bg)
}
.dot-red{
  width:7px;height:7px;border-radius:50%;
  background:var(--red);flex-shrink:0;
  animation:blink 2s infinite
}
.hero-stats{display:flex;flex-direction:column;gap:10px}
.stat{
  background:var(--card);border:1px solid var(--border);
  border-radius:var(--r2);padding:16px 20px;text-align:center;
  transition:border-color .15s
}
.stat:hover{border-color:var(--red)}
.stat-n{
  font-size:2rem;font-weight:800;color:var(--red);
  font-family:var(--mono);line-height:1
}
.stat-l{
  font-size:.65rem;font-weight:700;letter-spacing:.1em;
  text-transform:uppercase;color:var(--t3);margin-top:5px
}

/* ── MAIN ── */
.main{max-width:1160px;margin:0 auto;padding:48px 40px 72px}
.sec{margin-bottom:48px}
.sec-head{
  display:flex;align-items:center;gap:12px;margin-bottom:22px
}
.sec-head::before{
  content:'';width:3px;height:16px;
  background:var(--red);border-radius:2px;flex-shrink:0
}
.sec-head h2{
  font-size:.72rem;font-weight:700;letter-spacing:.12em;
  text-transform:uppercase;color:var(--t2)
}

/* ── ABOUT ── */
.about{
  background:var(--card);border:1px solid var(--border);
  border-left:3px solid var(--red);border-radius:var(--r2);
  padding:22px 26px
}
.about p{font-size:.88rem;color:var(--t2);line-height:1.85}

/* ── MODULE GRID ── */
.mods{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.mod{
  display:block;color:inherit;
  background:var(--card);border:1px solid var(--border);
  border-radius:var(--r2);overflow:hidden;
  transition:transform .15s,border-color .15s,box-shadow .15s
}
.mod:hover{
  transform:translateY(-4px);border-color:var(--border2);
  box-shadow:0 16px 48px rgba(0,0,0,.5)
}
.mod-line{height:3px}
.mod-line.g{background:var(--green)}
.mod-line.o{background:var(--orange)}
.mod-line.r{background:var(--red)}
.mod-body{padding:22px}
.mod-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.mod-ico{
  width:38px;height:38px;border-radius:var(--r);
  display:flex;align-items:center;justify-content:center
}
.mod-ico.g{background:var(--green-bg);color:var(--green)}
.mod-ico.o{background:var(--orange-bg);color:var(--orange)}
.mod-ico.r{background:var(--red-bg);color:var(--red)}
.mod-ico svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.tag{
  font-size:.62rem;font-weight:700;letter-spacing:.1em;
  padding:3px 10px;border-radius:20px;font-family:var(--mono);border:1px solid
}
.tag.g{color:var(--green);background:var(--green-bg);border-color:var(--green-bdr)}
.tag.o{color:var(--orange);background:var(--orange-bg);border-color:var(--orange-bdr)}
.tag.r{color:var(--red);background:var(--red-bg);border-color:var(--red-bdr)}
.mod h3{font-size:.95rem;font-weight:700;color:var(--t1);margin-bottom:8px;letter-spacing:-.01em}
.mod-desc{font-size:.82rem;color:var(--t2);line-height:1.7;margin-bottom:14px}
.mod-list{list-style:none;display:flex;flex-direction:column;gap:5px;margin-bottom:20px}
.mod-list li{
  font-size:.76rem;color:var(--t3);
  font-family:var(--mono);padding-left:14px;position:relative
}
.mod-list li::before{content:'›';position:absolute;left:0;color:var(--red)}
.mod-foot{
  display:flex;align-items:center;justify-content:space-between;
  padding-top:14px;border-top:1px solid var(--border);
  font-size:.78rem;font-weight:600;color:var(--t3);
  transition:color .15s
}
.mod:hover .mod-foot{color:var(--red)}
.mod-foot svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}

/* ── CHEATSHEET ── */
.cheats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.cheat{
  background:var(--card);border:1px solid var(--border);
  border-radius:var(--r2);padding:20px
}
.cheat-ttl{
  font-size:.64rem;font-weight:700;letter-spacing:.12em;
  text-transform:uppercase;font-family:var(--mono);margin-bottom:12px
}
.cheat-ttl.g{color:var(--green)}
.cheat-ttl.o{color:var(--orange)}
.cheat-ttl.r{color:var(--red)}
.code{
  background:var(--bg);border:1px solid var(--border);
  border-radius:var(--r);padding:12px 14px;
  font-family:var(--mono);font-size:.78rem;
  line-height:2;color:var(--t2)
}
.kw{color:var(--red)} .val{color:var(--blue)} .str{color:var(--orange)} .cm{color:var(--t3)}

/* ── FOOTER ── */
footer{border-top:1px solid var(--border);padding:22px 40px}
.foot{
  max-width:1160px;margin:0 auto;
  display:flex;align-items:center;gap:16px
}
.foot img{height:20px;opacity:.35}
.foot p{font-size:.72rem;color:var(--t3);font-family:var(--mono)}

@media(max-width:900px){
  .hero-inner,.mods,.cheats{grid-template-columns:1fr}
  .hero-stats{flex-direction:row}
  .stat{flex:1}
  .nav,.main,footer{padding-left:20px;padding-right:20px}
  .hero-inner{padding:40px 20px}
}
</style>
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="/"><img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers"></a>
  <div class="nav-menu">
    <a href="/" class="on">Dashboard</a>
    <a href="/basic/">Basic SQLi</a>
    <a href="/auth/">Auth Bypass</a>
    <a href="/blind/">Blind SQLi</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>

<div class="hero">
  <div class="hero-inner">
    <div>
      <div class="hero-eye"><i></i>Vulnerability Research</div>
      <h1>SQL Injection<br><b>Lab Environment</b></h1>
      <p class="hero-sub">Lingkungan praktik SQL Injection terstruktur untuk keperluan edukasi keamanan siber. Tersedia tiga modul dengan tingkat kompleksitas berbeda — dari basic hingga blind injection.</p>
      <div class="hero-note">
        <span class="dot-red"></span>
        FOR EDUCATIONAL USE ONLY &mdash; Gunakan hanya di environment lab terisolasi
      </div>
    </div>
    <div class="hero-stats">
      <div class="stat"><div class="stat-n">3</div><div class="stat-l">Modules</div></div>
      <div class="stat"><div class="stat-n">5</div><div class="stat-l">Challenges</div></div>
      <div class="stat"><div class="stat-n">5</div><div class="stat-l">Flags</div></div>
    </div>
  </div>
</div>

<div class="main">

  <div class="sec">
    <div class="sec-head"><h2>Tentang Lab</h2></div>
    <div class="about">
      <p>Lab ini dirancang untuk membantu memahami SQL Injection dari dasar hingga teknik yang lebih kompleks. Setiap modul memiliki tingkat kesulitan yang berbeda dan dilengkapi dengan hint serta visualisasi query SQL secara real-time, sehingga kamu dapat memahami mekanisme serangan di balik layar secara langsung.</p>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Lab Modules</h2></div>
    <div class="mods">

      <a href="/basic/" class="mod">
        <div class="mod-line g"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico g">
              <svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <span class="tag g">EASY</span>
          </div>
          <h3>Basic SQL Injection</h3>
          <p class="mod-desc">Eksploitasi query pencarian produk tanpa filtering. Pelajari error-based dan UNION-based injection untuk mengekstrak data dari database.</p>
          <ul class="mod-list">
            <li>Error-based SQLi</li>
            <li>UNION-based SQLi</li>
            <li>Cross-table extraction</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

      <a href="/auth/" class="mod">
        <div class="mod-line o"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico o">
              <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <span class="tag o">MEDIUM</span>
          </div>
          <h3>Auth Bypass + Filtering</h3>
          <p class="mod-desc">Bypass mekanisme autentikasi login menggunakan SQL injection. Tiga level filtering dengan tingkat kesulitan yang semakin meningkat.</p>
          <ul class="mod-list">
            <li>Login bypass klasik</li>
            <li>Comment-based bypass</li>
            <li>Filter evasion technique</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

      <a href="/blind/" class="mod">
        <div class="mod-line r"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico r">
              <svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </div>
            <span class="tag r">HARD</span>
          </div>
          <h3>Blind / Logic-Based SQLi</h3>
          <p class="mod-desc">Tidak ada error output. Gunakan boolean-based dan time-based blind injection untuk mengekstrak data satu karakter per karakter.</p>
          <ul class="mod-list">
            <li>Boolean-based blind</li>
            <li>Time-based blind</li>
            <li>Manual data extraction</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Quick Reference</h2></div>
    <div class="cheats">
      <div class="cheat">
        <div class="cheat-ttl g">SQL Comments</div>
        <div class="code"><span class="kw">--</span> <span class="cm">komentar satu baris</span><br><span class="kw">#</span>&nbsp; <span class="cm">komentar MySQL</span><br><span class="kw">/*</span> <span class="cm">komentar blok</span> <span class="kw">*/</span></div>
      </div>
      <div class="cheat">
        <div class="cheat-ttl o">Always True</div>
        <div class="code"><span class="val">1</span><span class="cm">=</span><span class="val">1</span><br><span class="str">'a'</span><span class="cm">=</span><span class="str">'a'</span><br><span class="val">1</span> <span class="kw">OR</span> <span class="val">1</span><span class="cm">=</span><span class="val">1</span></div>
      </div>
      <div class="cheat">
        <div class="cheat-ttl r">UNION Skeleton</div>
        <div class="code"><span class="str">'</span> <span class="kw">UNION SELECT</span> <span class="val">1,2,3</span><span class="cm">-- -</span><br><span class="str">'</span> <span class="kw">UNION SELECT</span> <span class="val">null,null</span><span class="cm">-- -</span></div>
      </div>
    </div>
  </div>

</div>

<footer>
  <div class="foot">
    <img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers">
    <p>SQL Injection Lab &mdash; For Educational Purposes Only &mdash; Do not use on unauthorized systems</p>
  </div>
</footer>
</body>
</html>
