<?php
require_once '../includes/db.php';
$conn = getDB();
$msg='';$mtype='';$qshown='';
$level = max(1, min(3, (int)($_GET['level'] ?? 1)));

function applyFilter($s, $l) {
    if ($l >= 2) $s = str_replace(['--','#'], '', $s);
    if ($l >= 3) $s = preg_replace('/\bOR\b|\bAND\b/i', '', $s);
    return $s;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $uf = applyFilter($u, $level);
    $pf = applyFilter($p, $level);
    $q = "SELECT id,username,role,secret FROM users WHERE username='$uf' AND password='$pf'";
    $qshown = "WHERE username = '$u'  AND  password = '$p'";
    if ($level > 1) $qshown .= "\n\n[Setelah filter L{$level}]\nWHERE username = '$uf'  AND  password = '$pf'";
    $res = $conn->query($q);
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $msg = "Login berhasil &mdash; selamat datang, <strong>" . htmlspecialchars($user['username'],ENT_QUOTES,'UTF-8') . "</strong> (role: {$user['role']})";
        if ($user['role'] === 'admin') $msg .= "<br><br>FLAG: <code style='font-family:var(--mono);color:var(--green);background:var(--gbg);padding:2px 8px;border-radius:4px;border:1px solid var(--gbdr)'>" . htmlspecialchars($user['secret'],ENT_QUOTES,'UTF-8') . "</code>";
        $mtype = 'ok';
    } else {
        $err = $conn->error ? ' &mdash; ' . htmlspecialchars($conn->error,ENT_QUOTES,'UTF-8') : '';
        $msg = "Username atau password salah.$err";
        $mtype = 'err';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auth Bypass — ID-Networkers Lab</title>
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
.tag.o{color:var(--orange);background:var(--obg);border-color:var(--obdr)}
.wrap{max-width:1000px;margin:0 auto;padding:28px 40px 72px}
.box{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:22px 24px;margin-bottom:14px}
.box-t{font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:16px;display:flex;align-items:center;gap:8px}
.box-t::before{content:'';width:3px;height:12px;background:var(--red);border-radius:2px;flex-shrink:0}

/* LEVEL TABS */
.lvl-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
.lvl-tab{padding:8px 18px;border-radius:var(--r);font-size:.8rem;font-weight:600;border:1px solid var(--bd);color:var(--t2);background:var(--el);transition:all .15s}
.lvl-tab:hover{border-color:var(--bd2);color:var(--t1)}
.lvl-tab.on{background:var(--rbg);border-color:var(--rbdr);color:var(--red)}

/* FORM */
.fg{margin-bottom:16px}
.fl{display:block;font-size:.66rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-family:var(--mono)}
.fi{width:100%;padding:10px 14px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--r);color:var(--t1);font-size:.88rem;font-family:var(--mono);outline:none;transition:border-color .15s}
.fi:focus{border-color:var(--red)}
.btn{display:inline-flex;align-items:center;padding:10px 26px;border:none;border-radius:var(--r);cursor:pointer;font-weight:700;font-size:.84rem;font-family:inherit;transition:all .15s}
.btn-r{background:var(--red);color:#fff}.btn-r:hover{background:#c1121f}

/* QUERY */
.qbox{background:var(--bg);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r);padding:14px 16px;font-family:var(--mono);font-size:.8rem;color:#a8c4e0;word-break:break-all;line-height:1.7;white-space:pre-wrap}
.ql{font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-weight:700}

/* ALERTS */
.alert{padding:13px 16px;border-radius:var(--r);font-size:.84rem;border:1px solid;margin-bottom:14px;line-height:1.6}
.a-ok{background:var(--gbg);border-color:var(--gbdr);color:var(--green)}
.a-err{background:var(--rbg);border-color:var(--rbdr);color:var(--red)}
.a-warn{background:var(--obg);border-color:var(--obdr);color:var(--orange)}
.a-info{background:rgba(56,189,248,.08);border-color:rgba(56,189,248,.2);color:var(--blue)}

/* HINTS */
.hint{background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:12px 16px;margin-bottom:8px}
.hint summary{cursor:pointer;font-size:.84rem;font-weight:600;color:var(--t2);list-style:none;display:flex;align-items:center;gap:8px;user-select:none}
.hint summary::-webkit-details-marker{display:none}
.hint summary::before{content:'▶';font-size:.58rem;color:var(--red);transition:transform .15s;flex-shrink:0}
.hint[open] summary::before{transform:rotate(90deg)}
.hint-body{margin-top:12px;padding-top:12px;border-top:1px solid var(--bd);font-size:.83rem;color:var(--t2);line-height:1.8}
.ic{background:var(--bg);border:1px solid var(--bd);padding:1px 6px;border-radius:4px;color:var(--green);font-family:var(--mono);font-size:.78rem}

footer{border-top:1px solid var(--bd);padding:22px 40px}
.foot{max-width:1000px;margin:0 auto;display:flex;align-items:center;gap:16px}
.foot img{height:20px;opacity:.35}
.foot p{font-size:.72rem;color:var(--t3);font-family:var(--mono)}
@media(max-width:700px){.nav,.wrap,.phdr,footer{padding-left:20px;padding-right:20px}}
</style>
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="/"><img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers"></a>
  <div class="nav-menu">
    <a href="/">Dashboard</a>
    <a href="/basic/">Basic SQLi</a>
    <a href="/auth/" class="on">Auth Bypass</a>
    <a href="/blind/">Blind SQLi</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span>/</span><span>Auth Bypass</span></div>
    <h1>Auth Bypass + Filtering <span class="tag o">MEDIUM</span></h1>
    <p>Bypass mekanisme autentikasi login menggunakan SQL injection. Setiap level menambahkan lapisan filtering yang semakin ketat.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Difficulty Level</div>
    <div class="lvl-tabs">
      <a href="/auth/?level=1" class="lvl-tab <?= $level==1?'on':'' ?>">Level 1 &mdash; No Filter</a>
      <a href="/auth/?level=2" class="lvl-tab <?= $level==2?'on':'' ?>">Level 2 &mdash; Filter -- / #</a>
      <a href="/auth/?level=3" class="lvl-tab <?= $level==3?'on':'' ?>">Level 3 &mdash; Filter OR / AND</a>
    </div>
    <?php if($level==1): ?>
      <div class="alert a-info">Tidak ada filtering sama sekali. Gunakan teknik SQL injection paling dasar untuk bypass login.</div>
    <?php elseif($level==2): ?>
      <div class="alert a-warn">Karakter <code class="ic">--</code> dan <code class="ic">#</code> dihapus dari input sebelum dimasukkan ke query. Temukan cara lain untuk menutup SQL query.</div>
    <?php else: ?>
      <div class="alert a-err">Selain <code class="ic">--</code> dan <code class="ic">#</code>, kata kunci <code class="ic">OR</code> dan <code class="ic">AND</code> juga dihapus. Diperlukan teknik yang lebih kreatif.</div>
    <?php endif; ?>
  </div>

  <div class="box">
    <div class="box-t">Login Form</div>
    <form method="POST" action="/auth/?level=<?= $level ?>">
      <div class="fg">
        <label class="fl">Username</label>
        <input class="fi" type="text" name="username" value="<?= htmlspecialchars($_POST['username']??'',ENT_QUOTES,'UTF-8') ?>" placeholder="username atau payload SQLi">
      </div>
      <div class="fg">
        <label class="fl">Password</label>
        <input class="fi" type="text" name="password" value="<?= htmlspecialchars($_POST['password']??'',ENT_QUOTES,'UTF-8') ?>" placeholder="password atau payload SQLi">
      </div>
      <button type="submit" class="btn btn-r">Login</button>
    </form>
  </div>

  <?php if ($qshown): ?>
  <div class="box">
    <div class="box-t">Executed Query</div>
    <div class="qbox"><div class="ql">WHERE Clause</div><?= htmlspecialchars($qshown,ENT_QUOTES,'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <?php if ($msg): ?>
  <div class="alert a-<?= $mtype ?>"><?= $msg ?></div>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints &mdash; Level <?= $level ?></div>

    <?php if($level==1): ?>
    <details class="hint">
      <summary>Hint 1 &mdash; Struktur query</summary>
      <div class="hint-body">Query asli: <code class="ic">WHERE username='INPUT' AND password='INPUT'</code><br>Tujuannya membuat kondisi menjadi <strong>TRUE</strong> dan membuang sisa query menggunakan komentar SQL.</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Payload klasik</summary>
      <div class="hint-body">
        Username: <code class="ic">admin'-- -</code> (password bebas apa saja)<br>
        Atau: <code class="ic">' OR 1=1-- -</code> di kolom username untuk login sebagai user pertama.
      </div>
    </details>
    <?php elseif($level==2): ?>
    <details class="hint">
      <summary>Hint 1 &mdash; Tanpa karakter komentar</summary>
      <div class="hint-body">
        Karena <code class="ic">--</code> dan <code class="ic">#</code> dihapus, gunakan <em>quote balancing</em> tanpa perlu komentar.<br>
        Username: <code class="ic">anything</code> &nbsp; Password: <code class="ic">' OR '1'='1</code>
      </div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Logika query</summary>
      <div class="hint-body">Query menjadi: <code class="ic">WHERE username='anything' AND password='' OR '1'='1'</code><br>Karena <code class="ic">'1'='1'</code> selalu TRUE, kondisi keseluruhan menjadi TRUE.</div>
    </details>
    <?php else: ?>
    <details class="hint">
      <summary>Hint 1 &mdash; Operator alternatif</summary>
      <div class="hint-body">Coba operator <code class="ic">||</code> (double pipe) sebagai pengganti OR di MySQL:<br>Username: <code class="ic">admin</code> &nbsp; Password: <code class="ic">x' || '1'='1</code></div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Pendekatan berbeda</summary>
      <div class="hint-body">Jika kamu tahu username yang valid, manipulasi hanya pada password field saja. Atau coba karakter yang menghasilkan kondisi always-true tanpa menggunakan kata kunci OR/AND.</div>
    </details>
    <?php endif; ?>
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
