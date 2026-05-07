<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();
if(!isset($_SESSION['system'])){
	$system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
	foreach($system as $k => $v){
		$_SESSION['system'][$k] = $v;
	}
}
ob_end_flush();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Login - <?php echo $_SESSION['system']['name'] ?></title>

<?php include('./header.php'); ?>
<?php 
if(isset($_SESSION['login_id'])){
	if(isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] === 1 && isset($_SESSION['login_username']) && strtolower(trim($_SESSION['login_username'])) === 'admin@event.com'){
		header("location:index.php?page=home");
	}else{
		header("location:../index.php?page=home");
	}
	exit;
}
?>

</head>
<style>
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	body {
		width: 100%;
		height: 100vh;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	main#main {
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
		padding: 20px;
	}

	.login-container {
		display: flex;
		width: 100%;
		max-width: 1000px;
		height: auto;
		background: white;
		border-radius: 15px;
		box-shadow: 0 20px 60px rgba(0,0,0,0.3);
		overflow: hidden;
	}

	.login-left {
		width: 55%;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 60px 40px;
		color: white;
		text-align: center;
	}

	.login-left h1 {
		font-size: 2.5em;
		margin-bottom: 20px;
		font-weight: 700;
	}

	.login-left p {
		font-size: 1.1em;
		line-height: 1.6;
		opacity: 0.95;
	}

	.admin-badge {
		display: inline-block;
		background: rgba(255,255,255,0.3);
		color: white;
		padding: 8px 20px;
		border-radius: 50px;
		font-size: 0.9em;
		margin-bottom: 20px;
		border: 1px solid rgba(255,255,255,0.5);
	}

	.login-right {
		width: 45%;
		padding: 60px 40px;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.login-right h2 {
		color: #333;
		font-size: 1.8em;
		margin-bottom: 10px;
		font-weight: 600;
	}

	.login-subtitle {
		color: #999;
		margin-bottom: 40px;
		font-size: 0.95em;
	}

	.form-group {
		margin-bottom: 25px;
	}

	.form-group label {
		display: block;
		color: #555;
		font-weight: 500;
		margin-bottom: 8px;
		font-size: 0.95em;
	}

	.form-group input {
		width: 100%;
		padding: 12px 16px;
		border: 2px solid #e0e0e0;
		border-radius: 8px;
		font-size: 1em;
		transition: all 0.3s ease;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}

	.form-group input:focus {
		outline: none;
		border-color: #667eea;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}

	.form-group input::placeholder {
		color: #bbb;
	}

	.checkbox-group {
		display: flex;
		align-items: center;
		margin-bottom: 30px;
	}

	.checkbox-group input[type="checkbox"] {
		width: 18px;
		height: 18px;
		margin-right: 8px;
		cursor: pointer;
	}

	.checkbox-group label {
		margin: 0;
		color: #666;
		font-size: 0.9em;
		cursor: pointer;
	}

	.forgot-link {
		text-align: right;
		margin-top: -15px;
		margin-bottom: 25px;
	}

	.forgot-link a {
		color: #667eea;
		text-decoration: none;
		font-size: 0.9em;
		transition: color 0.3s ease;
	}

	.forgot-link a:hover {
		color: #764ba2;
		text-decoration: underline;
	}

	.btn-login {
		width: 100%;
		padding: 14px;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		border: none;
		border-radius: 8px;
		font-size: 1.05em;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.btn-login:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
	}

	.btn-login:active {
		transform: translateY(0);
	}

	.btn-login:disabled {
		opacity: 0.7;
		cursor: not-allowed;
	}

	.alert {
		padding: 14px 16px;
		border-radius: 8px;
		margin-bottom: 20px;
		display: flex;
		align-items: center;
		font-size: 0.95em;
	}

	.alert-danger {
		background-color: #fee;
		color: #c33;
		border-left: 4px solid #c33;
	}

	.alert-success {
		background-color: #efe;
		color: #3c3;
		border-left: 4px solid #3c3;
	}

	.alert i {
		margin-right: 10px;
		font-size: 1.1em;
	}

	.loading-spinner {
		display: inline-block;
		width: 16px;
		height: 16px;
		border: 3px solid rgba(255,255,255,.3);
		border-radius: 50%;
		border-top-color: white;
		animation: spin 0.8s linear infinite;
		margin-right: 8px;
	}

	@keyframes spin {
		to { transform: rotate(360deg); }
	}

	.form-error {
		color: #c33;
		font-size: 0.85em;
		margin-top: 4px;
		display: none;
	}

	@media (max-width: 768px) {
		.login-container {
			flex-direction: column;
		}

		.login-left {
			width: 100%;
			padding: 40px 30px;
		}

		.login-left h1 {
			font-size: 1.8em;
		}

		.login-right {
			width: 100%;
			padding: 40px 30px;
		}
	}
</style>

<body>
	<main id="main">
		<div class="login-container">
			<div class="login-left">
				<div class="admin-badge">
					<i class="fa fa-shield"></i> ADMIN CENTER
				</div>
				<h1><?php echo $_SESSION['system']['name'] ?></h1>
				<p>Manage your events, venues, registrations, and more from our powerful admin dashboard. Secure access for administrators only.</p>
			</div>

			<div class="login-right">
				<h2>Admin Login</h2>
				<p class="login-subtitle">Sign in to your administrator account</p>

				<form id="login-form">
					<div id="alert-container"></div>

					<div class="form-group">
						<label for="username">
							<i class="fa fa-user"></i> Email or Username
						</label>
						<input type="text" id="username" name="username" class="form-control" 
							placeholder="Enter your email or username" required>
						<div class="form-error" id="username-error"></div>
					</div>

					<div class="form-group">
						<label for="password">
							<i class="fa fa-lock"></i> Password
						</label>
						<input type="password" id="password" name="password" class="form-control" 
							placeholder="Enter your password" required>
						<div class="form-error" id="password-error"></div>
					</div>

					<div class="checkbox-group">
						<input type="checkbox" id="remember" name="remember" value="1">
						<label for="remember">Remember me on this device</label>
					</div>

					<button type="submit" class="btn-login" id="login-btn">
						<span id="btn-text">Login</span>
					</button>
				</form>
			</div>
		</div>
	</main>

	<script>
		$('#login-form').submit(function(e){
			e.preventDefault();
			
			// Clear previous errors
			$('.form-error').hide().text('');
			$('#alert-container').empty();
			
			// Validate form
			let username = $('#username').val().trim();
			let password = $('#password').val();
			let isValid = true;
			
			if(!username) {
				$('#username-error').text('Email or username is required').show();
				isValid = false;
			}
			
			if(!password) {
				$('#password-error').text('Password is required').show();
				isValid = false;
			}
			
			if(!isValid) return false;
			
			// Disable button and show loading state
			let btn = $('#login-btn');
			btn.prop('disabled', true);
			btn.html('<span class="loading-spinner"></span>Logging in...');
			
			$.ajax({
				url:'ajax.php?action=login2',
				method:'POST',
				data: {
					username: username,
					password: password,
					remember: $('#remember').is(':checked') ? 1 : 0
				},
				success:function(resp){
					if(resp == 1){
						$('#alert-container').html(
							'<div class="alert alert-success">' +
							'<i class="fa fa-check-circle"></i> Login successful! Redirecting...' +
							'</div>'
						);
						setTimeout(function(){
							location.href ='index.php?page=home';
						}, 1500);
					}else if(resp == 4){
						$('#alert-container').html(
							'<div class="alert alert-danger">' +
							'<i class="fa fa-exclamation-circle"></i> Restricted to system admin only.' +
							'</div>'
						);
						btn.prop('disabled', false);
						btn.html('Login');
					}else if(resp == 2){
						location.href ='voting.php';
					}else{
						$('#alert-container').html(
							'<div class="alert alert-danger">' +
							'<i class="fa fa-exclamation-circle"></i> Restricted to administrator.' +
							'</div>'
						);
						btn.prop('disabled', false);
						btn.html('Login');
					}
				},
				error:function(err){
					console.log(err);
					$('#alert-container').html(
						'<div class="alert alert-danger">' +
						'<i class="fa fa-exclamation-circle"></i> An error occurred. Please try again.' +
						'</div>'
					);
					btn.prop('disabled', false);
					btn.html('Login');
				}
			});
		});

		// Clear error on input focus
		$('#username, #password').focus(function(){
			$(this).next('.form-error').hide();
		});
	</script>	
</body>
</html>