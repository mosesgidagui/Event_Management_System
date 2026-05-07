<?php if(isset($_SESSION['login_id']) && isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] == 3): ?>
<script>
	location.href = 'index.php?page=home'
</script>
<?php return; endif; ?>
<?php if(isset($_SESSION['login_id']) && isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] == 1 && isset($_SESSION['login_username']) && strtolower(trim($_SESSION['login_username'])) == 'admin@event.com'): ?>
<script>
	location.href = 'admin/index.php?page=home'
</script>
<?php return; endif; ?>
<section class="page-section" style="padding-top: 6rem;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-5 col-md-8">
				<div class="card shadow-lg border-0">
					<div class="card-body p-4 p-md-5">
						<h4 class="text-center mb-4">Student Login</h4>
						<form id="login-form">
							<div class="form-group">
								<label>Email or Username</label>
								<input type="text" class="form-control" name="username" required>
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="password" class="form-control" name="password" required>
							</div>
							<button class="btn btn-primary btn-block">Login</button>
						</form>
						<div class="text-center mt-3">
							<a href="index.php?page=signup">Create a student account</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'admin/ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast('Login successful','success')
					setTimeout(function(){
						location.href = 'index.php?page=home'
					},1000)
				}else if(resp == 4){
					alert_toast('Restricted to student accounts. Use the admin login form.','warning')
					end_load()
				}else{
					alert_toast('Invalid email or password','danger')
					end_load()
				}
			}
		})
	})
</script>