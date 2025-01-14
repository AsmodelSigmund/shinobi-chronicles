<?php

# Begin standard auth
require_once __DIR__ . "/../classes.php";

$system = API::init();

try {
    $player = Auth::getUserFromSession($system);
    $player->loadData(User::UPDATE_NOTHING);
} catch(RuntimeException $e) {
    API::exitWithException($e, system: $system);
}
# End standard auth

try {
    include __DIR__ . '/../pages/inbox.php';

    $request = $system->db->clean($_POST['request']);
    switch($request) {
        case 'LoadConvoList':
            $response = LoadConvoList($system, $player);
            break;

        case 'ViewConvo':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);

            $response = ViewConvo($system, $player, $requested_convo_id);
            break;

        case 'LoadNextPage':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $oldest_message_id = $system->db->clean($_POST['oldest_message_id']);

            $response = LoadNextPage($system, $player, $requested_convo_id, $oldest_message_id);
            break;

        case 'CheckForNewMessages':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $timestamp = $system->db->clean($_POST['timestamp']);

            $response = CheckForNewMessages($system, $player, $requested_convo_id, $timestamp);
            break;

        case 'SendMessage':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $message = $system->db->clean($_POST['message']);

            $response = SendMessage($system, $player, $requested_convo_id, $message);
            break;

        case 'ChangeTitle':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $new_title = $system->db->clean($_POST['new_title']);

            $response = ChangeTitle($system, $player, $requested_convo_id, $new_title);
            break;

        case 'AddPlayer':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $new_player = $system->db->clean($_POST['new_player']);

            $response = AddPlayer($system, $player, $requested_convo_id, $new_player);
            break;

        case 'RemovePlayer':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);
            $remove_player = $system->db->clean($_POST['remove_player']);

            $response = RemovePlayer($system, $player, $requested_convo_id, $remove_player);
            break;

        case 'LeaveConversation':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);

            $response = LeaveConversation($system, $player, $requested_convo_id);
            break;

        case 'CreateNewConvo':
            $members = $system->db->clean($_POST['members']);
            $title = $system->db->clean($_POST['title']);
            $message = $system->db->clean($_POST['message']);

            $response = CreateNewConvo($system, $player, $members, $title, $message);
            break;

        case 'ToggleMute':
            $requested_convo_id = $system->db->clean($_POST['requested_convo_id']);

            $response = ToggleMute($system, $player, $requested_convo_id);
            break;

        default:
            API::exitWithError(message: "Invalid request!", system: $system);
    }

    API::exitWithData(
        data: [
            'request' => $request,
            'response_data' => $response->response_data
        ],
        errors: $response->errors,
        debug_messages: $system->debug_messages,
        system: $system,
    );
} catch (Throwable $e) {
    API::exitWithException($e, system: $system);
}

