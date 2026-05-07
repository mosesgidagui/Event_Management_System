<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include 'admin/db_connect.php';
$search = trim($_GET['search'] ?? '');
$dateFilter = trim($_GET['date'] ?? '');
$category = trim($_GET['category'] ?? 'all');
$where = " WHERE date(e.schedule) >= '".$conn->real_escape_string(date('Y-m-d'))."' AND e.type = 1";
if($search !== ''){
    $searchEsc = $conn->real_escape_string($search);
    $where .= " AND (e.event LIKE '%$searchEsc%' OR e.description LIKE '%$searchEsc%' OR v.venue LIKE '%$searchEsc%')";
}
if($dateFilter !== ''){
    $where .= " AND date(e.schedule) = '".$conn->real_escape_string($dateFilter)."'";
}
if($category !== '' && $category !== 'all'){
    $categoryEsc = $conn->real_escape_string($category);
    $where .= " AND e.category = '$categoryEsc'";
}
$event = $conn->query("SELECT e.*,v.venue FROM events e inner join venue v on v.id=e.venue_id $where order by unix_timestamp(e.schedule) asc");
while($row = $event->fetch_assoc()):
    $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
    unset($trans['"'], $trans['<'], $trans['>'], $trans['<h2']);
    $desc = strtr(html_entity_decode($row['description']),$trans);
    $desc=str_replace(array("<li>","</li>"), array("",","), $desc);
?>
<div class="card event-list mx-auto" data-id="<?php echo $row['id'] ?>" style="max-width:900px;">
    <div class="card-body text-center">
        <div class="align-items-center justify-content-center h-100">
            <div>
                <h3><b class="filter-txt"><?php echo ucwords($row['event']) ?></b></h3>
                <div><small><p><b><i class="fa fa-calendar"></i> <?php echo date("F d, Y h:i A",strtotime($row['schedule'])) ?></b></p></small></div>
                <div><small><p><b>Category:</b> <?php echo ucwords($row['category'] ?? 'General') ?></p></small></div>
                <hr>
                <larger class="truncate filter-txt"><?php echo strip_tags($desc) ?></larger>
                <br>
                <hr class="divider"  style="max-width: calc(80%)">
                <div class="d-flex justify-content-center">
                    <button class="btn btn-success mr-2 rsvp_btn" data-id="<?php echo $row['id'] ?>">RSVP</button>
                    <button class="btn btn-primary read_more" data-id="<?php echo $row['id'] ?>">Read More</button>
                </div>
            </div>
        </div>
        

    </div>
</div>
<br>
<?php endwhile; ?>
