<?php include 'admin/db_connect.php'; ?>
<?php
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 3){
?>
<section class="page-section" style="padding-top: 6rem;">
	<div class="container">
		<div class="alert alert-info">
			Please log in with your student account to view your RSVP history.
			<div class="mt-3">
				<a class="btn btn-primary" href="index.php?page=login">Login</a>
				<a class="btn btn-outline-primary" href="index.php?page=signup">Sign Up</a>
			</div>
		</div>
	</div>
</section>
<?php
return;
}
$email = $conn->real_escape_string($_SESSION['login_username']);
$rsvps = $conn->query("SELECT a.*, e.event, e.schedule, e.type, e.payment_type, e.amount, v.venue FROM audience a INNER JOIN events e ON e.id = a.event_id INNER JOIN venue v ON v.id = e.venue_id WHERE a.email = '$email' ORDER BY e.schedule DESC");
?>
<section class="page-section" style="padding-top: 6rem;">
	<div class="container">
		<div class="card shadow-sm">
			<div class="card-body">
				<h4 class="mb-4">My RSVP History</h4>
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Event</th>
								<th>Venue</th>
								<th>Date</th>
								<th>Category</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php if($rsvps->num_rows > 0): while($row = $rsvps->fetch_assoc()): ?>
							<tr>
								<td><?php echo ucwords($row['event']) ?></td>
								<td><?php echo ucwords($row['venue']) ?></td>
								<td><?php echo date('M d, Y h:i A', strtotime($row['schedule'])) ?></td>
								<td><?php echo $row['type'] == 1 ? 'Public' : 'Private' ?></td>
								<td><?php echo $row['status'] == 1 ? 'Confirmed' : ($row['status'] == 2 ? 'Cancelled' : 'For Verification') ?></td>
							</tr>
							<?php endwhile; else: ?>
							<tr>
								<td colspan="5" class="text-center text-muted">No RSVPs yet.</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>