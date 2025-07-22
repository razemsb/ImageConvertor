<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_auth']) && $_SESSION['user_auth'] === true) {
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user']['id'],
            'username' => $_SESSION['user']['username']
        ]
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}
