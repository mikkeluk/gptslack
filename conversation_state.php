<?php
function get_state($user) {
    $states = json_decode(file_get_contents('state.json'), true) ?? [];
    return $states[$user] ?? ['step' => 0, 'answers' => []];
}

function save_state($user, $state) {
    $states = json_decode(file_get_contents('state.json'), true) ?? [];
    $states[$user] = $state;
    file_put_contents('state.json', json_encode($states));
}

function reset_state($user) {
    $states = json_decode(file_get_contents('state.json'), true) ?? [];
    unset($states[$user]);
    file_put_contents('state.json', json_encode($states));
}
