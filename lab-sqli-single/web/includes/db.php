<?php
// includes/db.php

function getDB() {
    $conn = new mysqli('127.0.0.1', 'labuser', 'labpass123', 'labsqli', 3306);

    if ($conn->connect_error) {
        die("<div style='background:#4a0010;color:#e94560;padding:15px;border-radius:6px;font-family:monospace;'>
            ❌ DB Error: " . $conn->connect_error . "<br>
            <small>Tunggu 5-10 detik lalu refresh (MySQL masih starting...)</small>
        </div>");
    }

    return $conn;
}

function sanitize($input) {
    return htmlspecialchars((string)$input, ENT_QUOTES, 'UTF-8');
}
?>
