<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function create_shortcut_card($summary) {
    $client = new \GuzzleHttp\Client();
    $lines = explode("\n", $summary);
    $title = trim(str_replace('Title:', '', $lines[0] ?? 'Untitled Bug'));
    $description = implode("\n", array_slice($lines, 1));

    $res = $client->post('https://api.app.shortcut.com/api/v3/stories', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Shortcut-Token' => $_ENV['SHORTCUT_API_TOKEN']
        ],
        'json' => [
            'name' => $title,
            'description' => $description,
            'project_id' => (int) $_ENV['SHORTCUT_PROJECT_ID'],
            'story_type' => 'bug'
        ]
    ]);

    return json_decode($res->getBody(), true);
}
