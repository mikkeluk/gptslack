<?php
require 'vendor/autoload.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['type']) && $data['type'] === 'url_verification') {
    echo $data['challenge'];
    exit;
}

http_response_code(200);
ignore_user_abort(true);
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

if (isset($data['event']) && $data['event']['type'] === 'message' && !isset($data['event']['bot_id'])) {
    $payload = base64_encode(json_encode($data));
    exec("php process_message.php $payload > /dev/null 2>&1 &");
}
