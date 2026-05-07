<?php
require 'admin/db_connect.php';
// create a default venue if none exists
$vid = 0;
$chk = $conn->query("SELECT id FROM venue LIMIT 1");
if($chk && $chk->num_rows > 0){
    $vid = $chk->fetch_assoc()['id'];
} else {
    $conn->query("INSERT INTO venue set venue='Main Hall', address='123 Campus Ave', description='Main campus hall', rate=0");
    $vid = $conn->insert_id;
}
$events = [
    ['Career Fair','Career',$vid, date('Y-m-d H:i:s', strtotime('+7 days')),100,1,1,0,'A career fair connecting students with top employers.'],
    ['Tech Talks','Technology',$vid, date('Y-m-d H:i:s', strtotime('+14 days')),80,1,1,0,'Short talks from industry professionals about current tech trends.'],
    ['Art Expo','Arts',$vid, date('Y-m-d H:i:s', strtotime('+21 days')),50,1,1,0,'Showcase of student art and interactive workshops.']
];
foreach($events as $ev){
    $event = $conn->real_escape_string($ev[0]);
    $category = $conn->real_escape_string($ev[1]);
    $venue_id = (int)$ev[2];
    $schedule = $conn->real_escape_string($ev[3]);
    $capacity = (int)$ev[4];
    $payment_type = (int)$ev[5];
    $type = (int)$ev[6];
    $amount = (float)$ev[7];
    $description = $conn->real_escape_string($ev[8]);
    // avoid duplicates by event name + schedule
    $chk = $conn->query("SELECT * FROM events WHERE event = '$event' AND schedule = '$schedule'");
    if($chk && $chk->num_rows > 0) continue;
    $conn->query("INSERT INTO events set event='$event', category='$category', venue_id=$venue_id, schedule='$schedule', audience_capacity=$capacity, payment_type=$payment_type, type=$type, amount=$amount, banner='', description='".htmlentities($description)."', date_created=NOW()");
}
echo json_encode(['status'=>1,'message'=>'Seed complete']);
