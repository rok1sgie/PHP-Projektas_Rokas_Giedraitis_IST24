<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$user = new User($db->connect());
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $repeatPassword = $_POST['repeat_password'] ?? '';

        if ($password !== $repeatPassword) {
            throw new Exception('Slaptažodžiai nesutampa.');
        }

        $user->register($username, $password);
        $message = 'Registracija sėkminga. Dabar galite prisijungti.';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

include __DIR__ . '/partials/header.php';
?>

<h1>Registracija</h1>
<form method="post" class="card form-card">
    <label>Vartotojo vardas</label>
    <input type="text" name="username" required>

    <label>Slaptažodis</label>
    <input type="password" name="password" required>

    <label>Pakartokite slaptažodį</label>
    <input type="password" name="repeat_password" required>

    <button type="submit">Registruotis</button>
</form>

<p class="message"><?= htmlspecialchars($message) ?></p>
<p><a href="login.php">Jau turite paskyrą? Prisijunkite</a></p>

<?php include __DIR__ . '/partials/footer.php'; ?>
