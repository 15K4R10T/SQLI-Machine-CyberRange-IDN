<?php
require_once '../includes/db.php';
$conn = getDB();
$results = []; $qshown = ''; $error = ''; $search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $q = "SELECT id, name, description, price, category FROM products WHERE name LIKE '%$search%'";
    $qshown = $q;
    $res = $conn->query($q);
    if ($res === false) $error = $conn->error;
    elseif ($res !== true) while ($row = $res->fetch_assoc()) $results[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic SQLi — ID-Networkers Lab</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:#080b13;color:#dde4ef;min-height:100vh;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
:root{
  --bg:#080b13;--surface:#0e1420;--card:#111827;--el:#161e2e;
  --bd:#1d2b3a;--bd2:#263446;
  --red:#e63946;--rbg:rgba(230,57,70,.1);--rbdr:rgba(230,57,70,.2);
  --green:#22c55e;--gbg:rgba(34,197,94,.08);--gbdr:rgba(34,197,94,.2);
  --orange:#f59e0b;
  --t1:#dde4ef;--t2:#7b8fa8;--t3:#3d5168;
  --mono:'Courier New',monospace;--r:8px;--r2:12px
}

/* NAV */
.nav{position:sticky;top:0;z-index:100;background:rgba(8,11,19,.96);backdrop-filter:blur(16px);border-bottom:1px solid var(--bd);height:60px;display:flex;align-items:center;padding:0 40px;gap:32px}
.nav-logo img{height:28px;display:block}
.nav-menu{display:flex;align-items:center;gap:2px;margin:0 auto}
.nav-menu a{font-size:.8rem;font-weight:600;letter-spacing:.02em;color:var(--t2);padding:6px 16px;border-radius:6px;transition:all .15s}
.nav-menu a:hover{color:var(--t1);background:var(--el)}
.nav-menu a.on{color:#fff;background:var(--red)}
.nav-pill{font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red);border:1px solid var(--rbdr);border-radius:20px;padding:4px 12px;white-space:nowrap}

/* PAGE HEADER */
.phdr{background:var(--surface);border-bottom:1px solid var(--bd);padding:32px 40px}
.phdr-in{max-width:1000px;margin:0 auto}
.bc{display:flex;align-items:center;gap:6px;font-size:.7rem;color:var(--t3);font-family:var(--mono);margin-bottom:10px}
.bc a{color:var(--t3);transition:color .15s}.bc a:hover{color:var(--red)}.bc span{color:var(--t3)}
.phdr h1{font-size:1.5rem;font-weight:800;letter-spacing:-.02em;margin-bottom:6px;display:flex;align-items:center;gap:10px}
.phdr p{font-size:.86rem;color:var(--t2)}

/* TAGS */
.tag{font-size:.6rem;font-weight:700;letter-spacing:.1em;padding:3px 10px;border-radius:20px;font-family:var(--mono);border:1px solid}
.tag.g{color:var(--green);background:var(--gbg);border-color:var(--gbdr)}
.tag.r{color:var(--red);background:var(--rbg);border-color:var(--rbdr)}

/* WRAP & BOXES */
.wrap{max-width:1000px;margin:0 auto;padding:28px 40px 72px}
.box{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:22px 24px;margin-bottom:14px}
.box-t{font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:16px;display:flex;align-items:center;gap:8px}
.box-t::before{content:'';width:3px;height:12px;background:var(--red);border-radius:2px;flex-shrink:0}

/* OBJECTIVES */
.obj-list{list-style:none;display:flex;flex-direction:column;gap:10px}
.obj-list li{display:flex;align-items:flex-start;gap:12px;font-size:.86rem;color:var(--t2);line-height:1.5}
.obj-n{width:22px;height:22px;border-radius:50%;background:var(--rbg);border:1px solid var(--rbdr);color:var(--red);font-size:.64rem;font-weight:700;font-family:var(--mono);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.ic{background:var(--bg);border:1px solid var(--bd);padding:1px 6px;border-radius:4px;color:var(--green);font-family:var(--mono);font-size:.78rem}

/* FORM */
.frow{display:flex;gap:10px;align-items:flex-end}
.fg{margin-bottom:14px;flex:1}
.fl{display:block;font-size:.66rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-family:var(--mono)}
.fi{width:100%;padding:10px 14px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--r);color:var(--t1);font-size:.88rem;font-family:var(--mono);outline:none;transition:border-color .15s}
.fi:focus{border-color:var(--red)}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border:none;border-radius:var(--r);cursor:pointer;font-weight:700;font-size:.82rem;font-family:inherit;transition:all .15s;white-space:nowrap}
.btn-r{background:var(--red);color:#fff}.btn-r:hover{background:#c1121f}
.btn-g{background:var(--el);color:var(--t2);border:1px solid var(--bd)}.btn-g:hover{color:var(--t1);border-color:var(--bd2)}

/* QUERY BOX */
.qbox{background:var(--bg);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r);padding:14px 16px;font-family:var(--mono);font-size:.8rem;color:#a8c4e0;word-break:break-all;line-height:1.7;white-space:pre-wrap;margin-bottom:14px}
.ql{font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-weight:700}

/* TABLE */
.tbl-wrap{overflow-x:auto;border-radius:var(--r2);border:1px solid var(--bd)}
.tbl{width:100%;border-collapse:collapse;font-size:.82rem}
.tbl th{background:var(--el);color:var(--t3);font-size:.64rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:10px 14px;text-align:left;border-bottom:1px solid var(--bd)}
.tbl td{padding:9px 14px;border-bottom:1px solid var(--bd);color:var(--t2);font-family:var(--mono)}
.tbl tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:var(--el);color:var(--t1)}

/* ALERTS */
.alert{padding:12px 16px;border-radius:var(--r);font-size:.84rem;font-family:var(--mono);border:1px solid;margin-bottom:14px;line-height:1.6}
.a-ok{background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.2);color:var(--green)}
.a-err{background:var(--rbg);border-color:var(--rbdr);color:var(--red)}
.a-warn{background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2);color:var(--orange)}

/* HINTS */
.hint{background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:12px 16px;margin-bottom:8px}
.hint summary{cursor:pointer;font-size:.84rem;font-weight:600;color:var(--t2);list-style:none;display:flex;align-items:center;gap:8px;user-select:none}
.hint summary::-webkit-details-marker{display:none}
.hint summary::before{content:'▶';font-size:.58rem;color:var(--red);transition:transform .15s;flex-shrink:0}
.hint[open] summary::before{transform:rotate(90deg)}
.hint-body{margin-top:12px;padding-top:12px;border-top:1px solid var(--bd);font-size:.83rem;color:var(--t2);line-height:1.8}
.hint-body .ic{background:var(--bg);border:1px solid var(--bd);padding:1px 6px;border-radius:4px;color:var(--green);font-family:var(--mono);font-size:.78rem}

/* FOOTER */
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
    <a href="/basic/" class="on">Basic SQLi</a>
    <a href="/auth/">Auth Bypass</a>
    <a href="/blind/">Blind SQLi</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span>/</span><span>Basic SQL Injection</span></div>
    <h1>Basic SQL Injection <span class="tag g">EASY</span></h1>
    <p>Eksploitasi query pencarian tanpa filtering &mdash; input langsung dimasukkan ke SQL query tanpa sanitasi apapun.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Konfirmasi adanya SQL injection dengan karakter <code class="ic">'</code></span></li>
      <li><div class="obj-n">2</div><span>Tentukan jumlah kolom query menggunakan <code class="ic">ORDER BY</code></span></li>
      <li><div class="obj-n">3</div><span>Tampilkan semua produk termasuk yang berstatus tersembunyi</span></li>
      <li><div class="obj-n">4</div><span>Gunakan <code class="ic">UNION SELECT</code> untuk membaca tabel <code class="ic">users</code></span></li>
      <li><div class="obj-n">5</div><span>Ekstrak kolom <code class="ic">secret</code> dari semua user di database</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Product Search</div>
    <form method="GET" action="/basic/">
      <div class="frow">
        <div class="fg" style="margin-bottom:0">
          <label class="fl">Nama Produk</label>
          <input class="fi" type="text" name="search" value="<?= htmlspecialchars($search,ENT_QUOTES,'UTF-8') ?>" placeholder="cari produk... atau masukkan payload SQLi" autofocus>
        </div>
        <button type="submit" class="btn btn-r">Cari</button>
        <?php if($search): ?><a href="/basic/" class="btn btn-g">Reset</a><?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($qshown): ?>
  <div class="box">
    <div class="box-t">Executed Query</div>
    <div class="qbox"><div class="ql">SQL</div><?= htmlspecialchars($qshown,ENT_QUOTES,'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <?php if ($error): ?>
  <div class="alert a-err"><strong>Database Error:</strong> <?= htmlspecialchars($error,ENT_QUOTES,'UTF-8') ?></div>
  <?php elseif (!empty($results)): ?>
  <div class="box">
    <div class="box-t">Result &mdash; <?= count($results) ?> row(s) returned</div>
    <div class="tbl-wrap">
      <table class="tbl">
        <thead><tr><?php foreach(array_keys($results[0]) as $c): ?><th><?= htmlspecialchars($c,ENT_QUOTES,'UTF-8') ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach($results as $row): ?><tr><?php foreach($row as $v): ?><td><?= htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8') ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
      </table>
    </div>
  </div>
  <?php elseif(isset($_GET['search'])): ?>
  <div class="alert a-warn">Tidak ada produk ditemukan untuk query tersebut.</div>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints</div>

    <details class="hint">
      <summary>Hint 1 &mdash; Konfirmasi SQL Injection</summary>
      <div class="hint-body">
        Masukkan tanda kutip tunggal: <code class="ic">'</code><br>
        Jika muncul database error, input tersebut vulnerable terhadap SQL injection.<br>
        Lanjut coba: <code class="ic">Laptop' OR '1'='1</code> untuk melihat lebih banyak hasil.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 2 &mdash; Hitung jumlah kolom</summary>
      <div class="hint-body">
        Gunakan <code class="ic">ORDER BY</code> secara bertahap:<br>
        <code class="ic">a' ORDER BY 1-- -</code> &rarr; <code class="ic">a' ORDER BY 2-- -</code> &rarr; dst.<br>
        Jika error muncul pada angka N, berarti query memiliki N-1 kolom.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 3 &mdash; UNION SELECT</summary>
      <div class="hint-body">
        Setelah mengetahui jumlah kolom (5 kolom), uji posisi kolom yang tampil di output:<br>
        <code class="ic">a' UNION SELECT 1,2,3,4,5-- -</code><br>
        Perhatikan angka mana yang muncul &mdash; itulah posisi yang bisa diisi data.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 4 &mdash; Ekstrak tabel users</summary>
      <div class="hint-body">
        Ganti angka pada UNION dengan nama kolom dari tabel users:<br>
        <code class="ic">a' UNION SELECT id,username,password,email,secret FROM users-- -</code>
      </div>
    </details>

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
