<?php

function uuidv4() {
    $d = random_bytes(16);
    $d[6] = chr((ord($d[6]) & 0x0f) | 0x40); // version
    $d[8] = chr((ord($d[8]) & 0x3f) | 0x80); // variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}

function pre($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function jsonResponse($data, $status = 200)
{
    header('Content-Type: application/json');
    http_response_code($status);

    echo json_encode($data);
    exit;
}

function jsonRequest(): array
{
    $raw = file_get_contents("php://input");

    if (!$raw) return [];

    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}


?>