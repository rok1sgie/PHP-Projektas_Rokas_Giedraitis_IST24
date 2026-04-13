<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'password_manager';
const DB_USER = 'root';
const DB_PASS = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}