<?php
    include __DIR__ . '/check-login.php';
    session_destroy();
    header('Location: ./login.php');
