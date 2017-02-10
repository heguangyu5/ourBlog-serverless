<?php

try {
    if (!isset($_SERVER['HTTP_REFERER'])) {
        throw new InvalidArgumentException('missing HTTP_REFERER');
    }
    if (!preg_match('#^http://([^/]+)#', $_SERVER['HTTP_REFERER'], $matches)) {
        throw new InvalidArgumentException('invalid HTTP_REFERER');
    }
    if ($_SERVER['SERVER_NAME'] != $matches[1]) {
        throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
    }
} catch (InvalidArgumentException $e) {
    exit;
}
