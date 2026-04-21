<?php

declare(strict_types=1);

class Encryptor
{
    private string $cipher = 'AES-256-CBC';

    private function buildKey(string $secret): string
    {
        return hash('sha256', $secret, true);
    }

    public function generateUserKey(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes / 2));
    }

    public function encrypt(string $plainText, string $secret): string
    {
        $key = $this->buildKey($secret);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = random_bytes($ivLength);

        $cipherText = openssl_encrypt(
            $plainText,
            $this->cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($cipherText === false) {
            throw new Exception('Šifravimas nepavyko.');
        }

        return base64_encode($iv . $cipherText);
    }

    public function decrypt(string $encryptedText, string $secret): string
    {
        $data = base64_decode($encryptedText, true);
        if ($data === false) {
            throw new Exception('Neteisingas užkoduotų duomenų formatas.');
        }

        $key = $this->buildKey($secret);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 0, $ivLength);
        $cipherText = substr($data, $ivLength);

        $plainText = openssl_decrypt(
            $cipherText,
            $this->cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plainText === false) {
            throw new Exception('Iššifravimas nepavyko.');
        }

        return $plainText;
    }
}