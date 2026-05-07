<?php include 'admin/db_connect.php' ?>
<?php
$eventData = null;
if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$eventId = (int) $_GET['id'];
	$qry = $conn->query("SELECT e.*, v.venue FROM events e INNER JOIN venue v ON v.id = e.venue_id WHERE e.id = {$eventId} LIMIT 1");
	if($qry && $qry->num_rows > 0){
		$eventData = $qry->fetch_assoc();
	}
}
?>

<style type="text/css">
	header.masthead {
		text-align: center;
	}

	#event-details-card {
		background: #ffffff;
		color: #1f2933;
		border: 1px solid #dbe3ea;
		border-radius: .5rem;
		box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.15);
	}

	#event-description {
		line-height: 1.7;
		font-size: 1rem;
	}

	#event-description p,
	#event-description li,
	#event-description span,
	#event-description div {
		color: #1f2933 !important;
	}
</style>

<?php if(!$eventData): ?>
	<section class="page-section" style="padding-top: 7rem;">
		<div class="container">
			<div class="alert alert-warning text-center">Event not found.</div>
		</div>
	</section>
<?php else: ?>
	<header class="masthead">
		<div class="container-fluid h-100">
			<div class="row h-100 align-items-center justify-content-center text-center">
				<div class="col-lg-8 align-self-end mb-4 pt-2 page-title">
					<h3 class="text-white"><b><?php echo ucwords($eventData['event']) ?></b></h3>
					<hr class="divider my-4" />
					<p class="text-white mb-0"><small><b><i>Venue: <?php echo ucwords($eventData['venue']) ?></i></b></small></p>
				</div>
			</div>
		</div>
	</header>

	<section class="page-section" style="padding-top: 2rem;">
		<div class="container">
			<div class="card" id="event-details-card">
				<div class="card-body p-4">
					<p class="mb-2"><b><i class="fa fa-calendar"></i> <?php echo date("F d, Y h:i A", strtotime($eventData['schedule'])) ?></b></p>
					<p class="mb-3"><b>Category:</b> <?php echo ucwords($eventData['category'] ?? 'General') ?></p>
					<div id="event-description"><?php echo html_entity_decode($eventData['description']); ?></div>
					<hr class="divider" style="max-width: calc(100%);" />
					<div class="text-center">
						<?php if(isset($_SESSION['login_id']) && $_SESSION['login_type'] == 3): ?>
							<button class="btn btn-primary" id="register" type="button">RSVP Now</button>
						<?php else: ?>
							<a class="btn btn-primary" href="index.php?page=login">Login to RSVP</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<script>
	$('#register').click(function(){
		uni_modal("Submit RSVP", "registration.php?event_id=<?php echo isset($eventData['id']) ? (int)$eventData['id'] : 0 ?>")
	})
</script>
