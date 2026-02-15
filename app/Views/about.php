<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>About</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>body{font-family:Poppins, sans-serif;background:#f9fafb;padding:24px}.card{max-width:900px;margin:32px auto;background:#fff;padding:24px;border-radius:10px}</style>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="card">
  <h1>About This Application</h1>
  <p>This is a simple school cash-management application migrated into a framework-like folder structure (views, controllers, models).</p>
  <p>If you want further refinements, I can extract header/footer partials and centralize CRUD logic into controllers.</p>
  <p><a href="?page=home">Back to Home</a></p>
</div>
</body>
</html>
