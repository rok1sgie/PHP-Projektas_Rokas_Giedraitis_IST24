<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/PasswordEntry.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$pdo = $db->connect();
$passwordEntry = new PasswordEntry($pdo);

$message = '';
$revealedPassword = '';
$revealedId = null;

try {
    $passwords = $passwordEntry->getAllByUserId((int)$_SESSION['user_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_password_id'])) {
        $passwordId = (int)$_POST['show_password_id'];
        $revealedPassword = $passwordEntry->decryptPassword(
            (int)$_SESSION['user_id'],
            $_SESSION['plain_password'],
            $passwordId
        );
        $revealedId = $passwordId;
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $passwords = [];
}

include __DIR__ . '/partials/header.php';
?>

<h1>Išsaugoti slaptažodžiai</h1>

<div class="card">
    <table>
        <thead>
        <tr>
            <th>Pavadinimas</th>
            <th>Sukūrimo data</th>
            <th>Veiksmas</th>
            <th>Slaptažodis</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($passwords as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= htmlspecialchars($item['created_at']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="show_password_id" value="<?= (int)$item['id'] ?>">
                        <button type="submit">Rodyti</button>
                    </form>
                </td>
                <td>
                    <?php if ($revealedId === (int)$item['id']): ?>
                        <strong><?= htmlspecialchars($revealedPassword) ?></strong>
                    <?php else: ?>
                        ********
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p class="message"><?= htmlspecialchars($message) ?></p>
<p><a href="dashboard.php">Grįžti į panelę</a></p>

<?php include __DIR__ . '/partials/footer.php'; ?>
