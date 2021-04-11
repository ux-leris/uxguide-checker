<?php

?>

<!doctype html>
<html lang="pt-BR">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">
		
		<!-- CSS Local -->
		<link rel="stylesheet" href="../../css/global.css">
		<link rel="stylesheet" href="../../css/login.css">

        <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title>Login</title>
	</head>

	<body>
		<!-- Navbar -->
		<nav class="navbar navbar-expand-lg navbar-light shadow" id="navbar">
			<a class="navbar-brand text-light" href="#">
				UXTools
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
			    <ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link text-light" href="./register.php">
							Register
						</a>						
					</li>
				</ul>
			</div>
        </nav>
        <div class="container" id="form-container">
            <div class="d-flex justify-content-center align-items-center" id="form">
                <form class="col-md-5" method="POST" action="../controllers/authenticate_user.php">
                    <h1 class="mb-5">Welcome back!</h1>
                    <div class="form-group">
                        <label>E-mail address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">
						<span>
							<i class="fas fa-sign-in-alt"></i>
						</span>
						Enter
					</button>
                </form>
            </div>
        </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../../js/jquery-3.5.1.js"></script>
        <script src="../../js/popper-base.js"></script>
        <script src="../../js/bootstrap/bootstrap.js"></script>
	</body>
</html>