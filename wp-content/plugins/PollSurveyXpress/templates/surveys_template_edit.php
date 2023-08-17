<?php
global $wpdb;

$poll_id = $_GET['poll_id']; // Get the poll ID from the URL parameter

// Query to fetch poll data
$query = $wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}polls_psx_polls
        WHERE poll_id = %d
    ", $poll_id);

$poll_data = $wpdb->get_row($query);
if (!$poll_data) {
    echo "Poll not found";
    return;
}

$poll_data_json = json_encode($poll_data);


echo ('Fuck');
