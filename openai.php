<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function summarize_conversation($answers) {
    $prompt = "You are a helpful assistant summarizing bug reports.\n\n";
    $prompt .= "User reported:\n";
    foreach ($answers as $i => $a) {
        $prompt .= ($i + 1) . ". $a\n";
    }
    $prompt .= "\nSummarize this into:\n- Title\n- Description\n- Steps to Reproduce\n- Expected vs Actual";

    $client = new \GuzzleHttp\Client();
    $res = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You summarize support issues.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]
    ]);
    $data = json_decode($res->getBody(), true);
    return $data['choices'][0]['message']['content'];
}
