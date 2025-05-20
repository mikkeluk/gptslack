<?php
function log_error($message) {
    $timestamp = date("Y-m-d H:i:s");
    $formatted = "[$timestamp] $message\n";
    file_put_contents(__DIR__ . '/log.txt', $formatted, FILE_APPEND);
}
