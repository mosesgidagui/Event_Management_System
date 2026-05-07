<div class="container-fluid">
	<?php if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 3): ?>
	<div class="alert alert-info mb-0">
		<p class="mb-2">Please log in or create a student account to RSVP for an event.</p>
		<div class="d-flex flex-wrap">
			<a class="btn btn-primary mr-2 mb-2" href="index.php?page=login">Login</a>
			<a class="btn btn-outline-primary mb-2" href="index.php?page=signup">Sign Up</a>
		</div>
	</div>
	<?php else: ?>
	<div id="msg"></div>
	<form action="" id="manage-register">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? $_GET['event_id'] :'' ?>">
		<input type="hidden" name="require_details" value="1">
		<div class="form-group">
			<label for="" class="control-label">Full Name</label>
			<input type="text" class="form-control" name="name"  value="<?php echo isset($name) ? $name : ($_SESSION['login_name'] ?? '') ?>" readonly>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Address</label>
			<textarea cols="30" rows = "2" required="" name="address" class="form-control"><?php echo isset($address) ? $address :'' ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" class="form-control" name="email"  value="<?php echo isset($email) ? $email : ($_SESSION['login_username'] ?? '') ?>" readonly>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Contact #</label>
			<input type="text" class="form-control" name="contact" pattern="[0-9]{7,15}"  value="<?php echo isset($contact) ? $contact :'' ?>" required>
		</div>
		<div class="form-group mb-0">
			<button type="submit" class="btn btn-primary" id="rsvp-btn">Submit RSVP</button>
		</div>
	</form>
</div>
<script>
	if(window.jQuery){
		jQuery('#uni_modal .modal-footer').hide();
	}

	$('#manage-register').submit(function(e){
		e.preventDefault()
		$('#msg').html('')
		var contact = (($('[name="contact"]').val() || '') + '').trim();
		var address = (($('[name="address"]').val() || '') + '').trim();
		var eventId = (($('[name="event_id"]').val() || '') + '').trim();
		if(!eventId){
			$('#msg').html('<div class="alert alert-danger">Invalid event selected.</div>')
			return false;
		}
		if(!address){
			$('#msg').html('<div class="alert alert-danger">Address is required.</div>')
			return false;
		}
		if(!/^[0-9]{7,15}$/.test(contact)){
			$('#msg').html('<div class="alert alert-danger">Contact must be 7 to 15 digits.</div>')
			return false;
		}
		start_load()
		$('#rsvp-btn').prop('disabled', true).text('Submitting...')
		$.ajax({
			url:'admin/ajax.php?action=save_register',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				resp = (resp+'').trim();
					if(resp==1){
						alert_toast("RSVP saved.",'success')
						end_load()
						uni_modal("","register_msg.php")
					}else if(resp==2){
						alert_toast("You have already RSVP'd for this event.",'warning')
						end_load()
					}else if(resp==3){
						$('#msg').html('<div class="alert alert-danger">Contact must be 7 to 15 digits.</div>')
						end_load()
					}else if(resp==4){
						$('#msg').html('<div class="alert alert-danger">Invalid event selected.</div>')
						end_load()
					}else if(resp==5){
						$('#msg').html('<div class="alert alert-warning">Please log in with a student account.</div>')
						end_load()
					}else if(resp==6){
						$('#msg').html('<div class="alert alert-danger">Address and contact are required.</div>')
						end_load()
					}else{
						$('#msg').html('<div class="alert alert-danger">Could not submit RSVP. Please check your details and try again.</div>')
						end_load()
				}
				$('#rsvp-btn').prop('disabled', false).text('Submit RSVP')
			},
			error:function(){
				end_load()
				$('#msg').html('<div class="alert alert-danger">Network error. Please try again.</div>')
				$('#rsvp-btn').prop('disabled', false).text('Submit RSVP')
			}
		})
	})
	<?php endif; ?>
</script>