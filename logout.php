<?php
session_start();

// Oturumu sonlandır
session_destroy();

// Remember me çerezini sil
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Ana sayfaya yönlendir
header('Location: index.php');
exit; 