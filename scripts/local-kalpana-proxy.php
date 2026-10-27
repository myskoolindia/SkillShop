<?php
// Serves Kalpana (the "Design My Lab" planner, ./kalpana) under /kalpana/ on the SkillShop address.
// Kalpana runs as its own Node service; this forwards requests to it with the /kalpana prefix removed.
$upstream = rtrim(getenv('KALPANA_UPSTREAM') ?: 'http://127.0.0.1:8787', '/');
$uri = $_SERVER['REQUEST_URI'] ?? '/kalpana/';
$target = $upstream . substr($uri, strlen('/kalpana'));

$forward = ['Expect:'];
foreach (['CONTENT_TYPE' => 'Content-Type', 'HTTP_AUTHORIZATION' => 'Authorization', 'HTTP_ACCEPT' => 'Accept', 'HTTP_ACCEPT_LANGUAGE' => 'Accept-Language', 'HTTP_RANGE' => 'Range'] as $key => $name) {
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        $forward[] = $name . ': ' . $_SERVER[$key];
    }
}
$forward[] = 'X-Forwarded-For: ' . ($_SERVER['REMOTE_ADDR'] ?? '');
$forward[] = 'X-Forwarded-Host: ' . ($_SERVER['HTTP_HOST'] ?? '');
$forward[] = 'X-Forwarded-Prefix: /kalpana';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$responseHeaders = [];
$curl = curl_init($target);
curl_setopt_array($curl, [
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => $forward,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 120, // AI-assisted planning can take a while
    CURLOPT_HEADERFUNCTION => function ($curl, $line) use (&$responseHeaders) {
        if (str_contains($line, ':')) {
            [$name, $value] = explode(':', $line, 2);
            $responseHeaders[] = [trim($name), trim($value)];
        }
        return strlen($line);
    },
]);
if (!in_array($method, ['GET', 'HEAD'], true)) {
    curl_setopt($curl, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}
$body = curl_exec($curl);
if ($body === false) {
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'The lab planner is not running. Start it with kalpana/run.sh.']);
    exit;
}
http_response_code((int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE));
foreach ($responseHeaders as [$name, $value]) {
    if (!in_array(strtolower($name), ['transfer-encoding', 'connection', 'keep-alive', 'content-length'], true)) {
        header($name . ': ' . $value, false);
    }
}
echo $body;
