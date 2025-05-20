<?php
require 'vendor/autoload.php';
require 'conversation_state.php';
require 'openai.php';
require 'shortcut.php';
require 'logger.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $payload = $argv[1] ?? '';
    if (!$payload) exit;

    $data = json_decode(base64_decode($payload), true);
    $event = $data['event'] ?? null;

    $user = $event['user'];
    $text = $event['text'];
    $channel = $event['channel'];

    $state = get_state($user);
    $step = $state['step'];
    $answers = $state['answers'];

    $client = new \GuzzleHttp\Client([
        'headers' => ['Authorization' => 'Bearer ' . $_ENV['SLACK_BOT_TOKEN']]
    ]);

    function send_message($channel, $text, $client) {
        $client->post('https://slack.com/api/chat.postMessage', [
            'json' => ['channel' => $channel, 'text' => $text]
        ]);
    }

    switch ($step) {
        case 0:
            send_message($channel, "Hi! What's the issue you're seeing?", $client);
            $state['step'] = 1;
            break;
        case 1:
            $answers[] = $text;
            send_message($channel, "Thanks! What was the expected behaviour?", $client);
            $state['step'] = 2;
            break;
        case 2:
            $answers[] = $text;
            send_message($channel, "And what actually happened?", $client);
            $state['step'] = 3;
            break;
        case 3:
            $answers[] = $text;
            send_message($channel, "Got it! Summarising and sending to Shortcut...", $client);
            $summary = summarize_conversation($answers);
            create_shortcut_card($summary);
            send_message($channel, "Done! I've created a Shortcut card for you.", $client);
            reset_state($user);
            exit;
    }

    $state['answers'] = $answers;
    save_state($user, $state);

} catch (Exception $e) {
    log_error("Error processing message: " . $e->getMessage());
}
