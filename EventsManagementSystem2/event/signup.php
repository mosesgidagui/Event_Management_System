<?php if(isset($_SESSION['login_id']) && $_SESSION['login_type'] == 3): ?>
<script>
	location.href = 'index.php?page=home'
</script>
<?php return; endif; ?>
<section class="page-section" style="padding-top: 6rem;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-md-9">
				<div class="card shadow-lg border-0">
					<div class="card-body p-4 p-md-5">
						<h4 class="text-center mb-4">Student Sign Up</h4>
						<form id="signup-form">
							<div class="form-group">
								<label>Full Name</label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control" name="email" required>
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="password" class="form-control" name="password" required>
							</div>
							<div class="form-group">
								<label>Confirm Password</label>
								<input type="password" class="form-control" name="cpassword" required>
							</div>
							<button class="btn btn-primary btn-block">Create Account</button>
						</form>
						<div class="text-center mt-3">
							<a href="index.php?page=login">Already have an account? Login</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
	$('#signup-form').submit(function(e){
		e.preventDefault()
		if($(this).find('[name="password"]').val() !== $(this).find('[name="cpassword"]').val()){
			alert_toast('Passwords do not match','danger')
			return false
		}
		start_load()
		$.ajax({
			url:'admin/ajax.php?action=signup',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast('Account created successfully','success')
					setTimeout(function(){
						location.href = 'index.php?page=home'
					},1000)
				}else if(resp == 2){
					alert_toast('Email already exists','danger')
					end_load()
				}else{
					alert_toast('Unable to create account','danger')
					end_load()
				}
			}
		})
	})
</script>