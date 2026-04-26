<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$user = new User($db->connect());
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $repeatPassword = $_POST['repeat_password'] ?? '';

        if ($newPassword !== $repeatPassword) {
            throw new Exception('Nauji slaptažodžiai nesutampa.');
        }

        $user->changePassword((int)$_SESSION['user_id'], $oldPassword, $newPassword);
        $message = 'Prisijungimo slaptažodis pakeistas sėkmingai. RAKTAS liko tas pats, tik peršifruotas.';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

include __DIR__ . '/partials/header.php';
?>

<h1>Keisti prisijungimo slaptažodį</h1>
<form method="post" class="card form-card">
    <label>Dabartinis slaptažodis</label>
    <input type="password" name="old_password" required>

    <label>Naujas slaptažodis</label>
    <input type="password" name="new_password" required>

    <label>Pakartokite naują slaptažodį</label>
    <input type="password" name="repeat_password" required>

    <button type="submit">Keisti</button>
</form>

<p class="message"><?= htmlspecialchars($message) ?></p>
<p><a href="dashboard.php">Grįžti į panelę</a></p>

<?php include __DIR__ . '/partials/footer.php'; ?>
