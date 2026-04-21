<?php

declare(strict_types=1);

require_once __DIR__ . '/Encryptor.php';

class User
{
    private PDO $db;
    private Encryptor $encryptor;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->encryptor = new Encryptor();
    }

    public function register(string $username, string $plainPassword): bool
    {
        $username = trim($username);

        if ($username === '' || $plainPassword === '') {
            throw new Exception('Visi laukai privalomi.');
        }

        if (mb_strlen($username) < 3) {
            throw new Exception('Vartotojo vardas per trumpas.');
        }

        if (strlen($plainPassword) < 6) {
            throw new Exception('Slaptažodis turi būti bent 6 simbolių.');
        }

        if ($this->findByUsername($username)) {
            throw new Exception('Toks vartotojo vardas jau egzistuoja.');
        }

        $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $userKey = $this->encryptor->generateUserKey(32);
        $encryptedKey = $this->encryptor->encrypt($userKey, $plainPassword);

        $stmt = $this->db->prepare(
            'INSERT INTO users (username, password_hash, encrypted_key) VALUES (:username, :password_hash, :encrypted_key)'
        );

        return $stmt->execute([
            ':username' => $username,
            ':password_hash' => $passwordHash,
            ':encrypted_key' => $encryptedKey,
        ]);
    }

    public function login(string $username, string $plainPassword): array|false
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($plainPassword, $user['password_hash'])) {
            return false;
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['plain_password'] = $plainPassword;

        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => trim($username)]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserKey(int $userId, string $plainPassword): string
    {
        $stmt = $this->db->prepare('SELECT encrypted_key FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception('Vartotojas nerastas.');
        }

        return $this->encryptor->decrypt($row['encrypted_key'], $plainPassword);
    }

    public function changePassword(int $userId, string $oldPlainPassword, string $newPlainPassword): bool
    {
        if (strlen($newPlainPassword) < 6) {
            throw new Exception('Naujas slaptažodis turi būti bent 6 simbolių.');
        }

        $user = $this->findById($userId);
        if (!$user) {
            throw new Exception('Vartotojas nerastas.');
        }

        if (!password_verify($oldPlainPassword, $user['password_hash'])) {
            throw new Exception('Senas slaptažodis neteisingas.');
        }

        $realKey = $this->encryptor->decrypt($user['encrypted_key'], $oldPlainPassword);
        $reEncryptedKey = $this->encryptor->encrypt($realKey, $newPlainPassword);
        $newHash = password_hash($newPlainPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'UPDATE users SET password_hash = :password_hash, encrypted_key = :encrypted_key WHERE id = :id'
        );

        $updated = $stmt->execute([
            ':password_hash' => $newHash,
            ':encrypted_key' => $reEncryptedKey,
            ':id' => $userId,
        ]);

        if ($updated) {
            $_SESSION['plain_password'] = $newPlainPassword;
        }

        return $updated;
    }
}
