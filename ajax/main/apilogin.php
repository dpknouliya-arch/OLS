

		$user = $_POST["user"];
		$password = $_POST["password"]; // plain

		$_SESSION['API_TOKEN'] = apiLogin($user, $password);