<?php
// Simple redirect to front controller in public/
// Keeps previous URLs working by forwarding to ?page=...
$target = './public/index.php';
$query = '';
if (isset($_SERVER['QUERY_STRING']) && trim($_SERVER['QUERY_STRING']) !== '') {
    $query = '?' . $_SERVER['QUERY_STRING'];
} else {
    $query = '?page=home';
}
header('Location: ' . $target . $query);
exit;
