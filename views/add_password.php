<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/PasswordGenerator.php';
require_once __DIR__ . '/../classes/PasswordEntry.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$pdo = $db->connect();
$passwordEntry = new PasswordEntry($pdo);

$message = '';
$generatedPassword = '';

if (isset($_POST['generate'])) {
    try {
        $length = (int)($_POST['length'] ?? 0);
        $lowercase = (int)($_POST['lowercase'] ?? 0);
        $uppercase = (int)($_POST['uppercase'] ?? 0);
        $numbers = (int)($_POST['numbers'] ?? 0);
        $specials = (int)($_POST['specials'] ?? 0);

        $generator = new PasswordGenerator($length, $lowercase, $uppercase, $numbers, $specials);
        $generatedPassword = $generator->generate();
        $message = 'Slaptažodis sėkmingai sugeneruotas.';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

if (isset($_POST['save'])) {
    try {
        $title = $_POST['title'] ?? '';
        $passwordToSave = $_POST['password_to_save'] ?? '';

        $passwordEntry->add(
            (int)$_SESSION['user_id'],
            $_SESSION['plain_password'],
            $title,
            $passwordToSave
        );

        $message = 'Slaptažodis sėkmingai išsaugotas.';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

include __DIR__ . '/partials/header.php';
?>

<h1>Generuoti ir išsaugoti slaptažodį</h1>

<div class="grid-2">
    <form method="post" class="card form-card">
        <h2>Generatorius</h2>

        <label>Bendras ilgis</label>
        <input type="number" name="length" min="1" required>

        <label>Mažosios raidės</label>
        <input type="number" name="lowercase" min="0" required>

        <label>Didžiosios raidės</label>
        <input type="number" name="uppercase" min="0" required>

        <label>Skaičiai</label>
        <input type="number" name="numbers" min="0" required>

        <label>Specialūs simboliai</label>
        <input type="number" name="specials" min="0" required>

        <button type="submit" name="generate">Generuoti</button>
    </form>

    <form method="post" class="card form-card">
        <h2>Išsaugojimas</h2>

        <label>Svetainės / programos pavadinimas</label>
        <input type="text" name="title" required>

        <label>Slaptažodis</label>
        <input type="text" name="password_to_save" value="<?= htmlspecialchars($generatedPassword ?: ($_POST['password_to_save'] ?? '')) ?>" required>

        <button type="submit" name="save">Išsaugoti</button>
    </form>
</div>

<?php if ($generatedPassword !== ''): ?>
    <div class="card">
        <p><strong>Sugeneruotas slaptažodis:</strong> <?= htmlspecialchars($generatedPassword) ?></p>
    </div>
<?php endif; ?>

<p class="message"><?= htmlspecialchars($message) ?></p>
<p><a href="dashboard.php">Grįžti į panelę</a></p>

<?php include __DIR__ . '/partials/footer.php'; ?>
