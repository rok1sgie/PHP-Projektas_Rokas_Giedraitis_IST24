<?php

declare(strict_types=1);

require_once __DIR__ . '/Encryptor.php';
require_once __DIR__ . '/User.php';

class PasswordEntry
{
    private PDO $db;
    private Encryptor $encryptor;
    private User $user;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->encryptor = new Encryptor();
        $this->user = new User($db);
    }

    public function add(int $userId, string $plainLoginPassword, string $title, string $plainPasswordToStore): bool
    {
        $title = trim($title);

        if ($title === '' || $plainPasswordToStore === '') {
            throw new Exception('Pavadinimas ir slaptažodis yra privalomi.');
        }

        $userKey = $this->user->getUserKey($userId, $plainLoginPassword);
        $encryptedPassword = $this->encryptor->encrypt($plainPasswordToStore, $userKey);

        $stmt = $this->db->prepare(
            'INSERT INTO passwords (user_id, title, encrypted_password) VALUES (:user_id, :title, :encrypted_password)'
        );

        return $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':encrypted_password' => $encryptedPassword,
        ]);
    }

    public function getAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, title, encrypted_password, created_at FROM passwords WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function decryptPassword(int $userId, string $plainLoginPassword, int $passwordId): string
    {
        $stmt = $this->db->prepare(
            'SELECT encrypted_password FROM passwords WHERE id = :id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute([
            ':id' => $passwordId,
            ':user_id' => $userId,
        ]);

        $row = $stmt->fetch();
        if (!$row) {
            throw new Exception('Slaptažodžio įrašas nerastas.');
        }

        $userKey = $this->user->getUserKey($userId, $plainLoginPassword);
        return $this->encryptor->decrypt($row['encrypted_password'], $userKey);
    }
}