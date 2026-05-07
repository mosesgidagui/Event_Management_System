<?php
if(session_status() == PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['login_id'])):
?>
<div class="container-fluid">
	<div class="alert alert-warning">You must <a href="index.php?page=login">log in</a> to request a venue booking.</div>
</div>
<?php return; endif; ?>

<div class="container-fluid">
	<div id="msg"></div>
	<form action="admin/ajax.php?action=save_book" id="manage-book" method="POST">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<input type="hidden" name="venue_id" value="<?php echo isset($_GET['venue_id']) ? $_GET['venue_id'] :'' ?>">
		<div class="form-group">
			<label for="" class="control-label">Full Name</label>
			<input type="text" class="form-control" name="name"  value="<?php echo isset($name) ? $name :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Address</label>
			<textarea cols="30" rows = "2" required="" name="address" class="form-control"><?php echo isset($address) ? $address :'' ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" class="form-control" name="email"  value="<?php echo isset($email) ? $email :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Contact #</label>
			<input type="text" class="form-control" name="contact" pattern="[0-9]{7,15}"  value="<?php echo isset($contact) ? $contact :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Duration</label>
			<input type="text" class="form-control" name="duration"  value="<?php echo isset($duration) ? $duration :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Desired Event Schedule</label>
			<input type="text" class="form-control datetimepicker" name="schedule"  value="<?php echo isset($schedule) ? $schedule :'' ?>" required>
			<small class="text-muted">Use format: YYYY/MM/DD HH:MM</small>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary mr-2">Request Booking</button>
			<button type="button" class="btn btn-secondary" id="cancel-booking">Cancel</button>
		</div>
	</form>
</div>
<script>
	if(window.jQuery){
		jQuery('#uni_modal .modal-footer').hide();
	}

	// initialize datetimepicker if available
	if(window.jQuery && typeof jQuery().datetimepicker === 'function'){
		$('.datetimepicker').datetimepicker({
			format:'Y/m/d H:i',
			startDate: '+3d'
		})
	}

	// Vanilla JS form handling (works without jQuery)
	(function(){
		var form = document.getElementById('manage-book');
		var msg = document.getElementById('msg');
		var cancelBtn = document.getElementById('cancel-booking');
		if(!form) return;
		if(cancelBtn){
			cancelBtn.addEventListener('click', function(){
				if(window.jQuery){
					jQuery('#uni_modal').modal('hide');
				}
			});
		}
		form.addEventListener('submit', function(e){
			e.preventDefault();
			msg.innerHTML = '';
			var fd = new FormData(form);
			var name = (fd.get('name') || '').trim();
			var address = (fd.get('address') || '').trim();
			var email = (fd.get('email') || '').trim();
			var contact = (fd.get('contact') || '').trim();
			var duration = (fd.get('duration') || '').trim();
			var schedule = (fd.get('schedule') || '').trim();
			var venue_id = (fd.get('venue_id') || '').trim();

			if(!name || !address || !email || !contact || !duration || !schedule || !venue_id){
				msg.innerHTML = '<div class="alert alert-danger">Please fill in all required fields.</div>';
				return false;
			}
			var emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
			if(!emailPattern.test(email)){
				msg.innerHTML = '<div class="alert alert-danger">Please enter a valid email address.</div>';
				return false;
			}
			if(!/^[0-9]+$/.test(contact)){
				msg.innerHTML = '<div class="alert alert-danger">Contact must contain digits only.</div>';
				return false;
			}
			if(!/^[0-9]{7,15}$/.test(contact)){
				msg.innerHTML = '<div class="alert alert-danger">Contact must be 7 to 15 digits.</div>';
				return false;
			}
			if(isNaN(Date.parse(schedule.replace(/\//g,'-')))){
				msg.innerHTML = '<div class="alert alert-danger">Please enter a valid schedule in YYYY/MM/DD HH:MM format.</div>';
				return false;
			}

			// send via fetch
			var submitBtn = form.querySelector('button[type="submit"]');
			if(submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Sending...';
			}

			fetch(form.action, { method: 'POST', body: fd })
			.then(function(r){ return r.text(); })
			.then(function(text){
				var resp = parseInt(text);
				if(resp === 1){
					msg.innerHTML = '<div class="alert alert-success">Booking request sent.</div>';
					// try to open modal if function exists
					if(typeof uni_modal === 'function') uni_modal('', 'book_msg.php');
					} else if(resp === 2){
					msg.innerHTML = '<div class="alert alert-danger">Please fill in all required fields.</div>';
				} else if(resp === 3){
					msg.innerHTML = '<div class="alert alert-danger">Contact must contain digits only.</div>';
				} else if(resp === 4){
					msg.innerHTML = '<div class="alert alert-danger">Please enter a valid email address.</div>';
					} else if(resp === 5){
						msg.innerHTML = '<div class="alert alert-warning">You must be logged in to request a booking. <a href="index.php?page=login">Login</a></div>';
				} else {
					msg.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
				}
			})
			.catch(function(err){
				console.error(err);
				msg.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
			})
			.finally(function(){
				if(submitBtn){ submitBtn.disabled = false; submitBtn.textContent = 'Request Booking'; }
			});

		});
	})();
</script>