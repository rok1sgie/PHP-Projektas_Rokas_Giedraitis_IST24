<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$user = new User($db->connect());
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($user->login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }

    $message = 'Neteisingas vartotojo vardas arba slaptažodis.';
}

include __DIR__ . '/partials/header.php';
?>

<h1>Prisijungimas</h1>
<form method="post" class="card form-card">
    <label>Vartotojo vardas</label>
    <input type="text" name="username" required>

    <label>Slaptažodis</label>
    <input type="password" name="password" required>

    <button type="submit">Prisijungti</button>
</form>

<p class="message"><?= htmlspecialchars($message) ?></p>
<p><a href="register.php">Neturite paskyros? Registruokitės</a></p>

<?php include __DIR__ . '/partials/footer.php'; ?>
