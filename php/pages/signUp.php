<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">

		<!-- CSS Local -->
		<link rel="stylesheet" href="../../css/styles/global.css">
		<link rel="stylesheet" href="../../css/styles/signUp.css">

		<title>Sign up</title>
	</head>

	<body>
		<nav class="navbar navbar-expand-lg">
			<span class="navbar-brand text-light">
				UX Guide Checker
			</span>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link text-light" href="./signin.php">
							Sign in
						</a>						
					</li>
				</ul>
			</div>
		</nav>

		<div class="container h-100 d-flex justify-content-center align-items-center">
			<div class="col-md-12 d-flex justify-content-center">
				<form class="col-md-6" method="POST" action="../controllers/signUp/signUp.php">
					<h1 class="mb-5">Sign up</h1>
					<div class="form-group">
						<label>Name</label>
						<input type="text" name="name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>E-mail address</label>
						<input type="email" name="email" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Password</label>
						<input id="password-input" type="password" name="password" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Confirm password</label>
						<input id="confirm-input" type="password" class="form-control" required>
						<small id="confirm-message" class="text-danger is-hidden">Must be equal to password</small>
					</div>
					<button id="signup-btn" type="submit" class="btn btn-success">
						<span>
							<i class="fas fa-user-plus"></i>
						</span>
						Sign up
					</button>
				</form>
			</div>
		</div>

		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="../../js/jquery-3.5.1.js"></script>
		<script src="../../js/popper-base.js"></script>
		<script src="../../js/bootstrap/bootstrap.js"></script>

		<script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<script src="../../js/pages/signup/confirmPassword.js"></script>
	</body>
</html>