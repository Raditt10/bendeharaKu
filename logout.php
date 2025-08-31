<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Kirim pesan lewat URL (GET)
header("Location: index.php?pesan=logout");
exit();
?>
