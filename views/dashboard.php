<?php
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include __DIR__ . '/partials/header.php';
?>

<h1>Vartotojo panelė</h1>
<div class="card">
    <p>Sveiki, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    <div class="menu-links">
        <a href="add_password.php">Generuoti / pridėti slaptažodį</a>
        <a href="list_passwords.php">Peržiūrėti išsaugotus slaptažodžius</a>
        <a href="change_password.php">Keisti prisijungimo slaptažodį</a>
        <a href="logout.php">Atsijungti</a>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
