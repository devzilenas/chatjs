<?

class Login {

	public static function user() {
		return User::load(self::loggedId());
	}

	public static function logUserIn($user) {
		$user->sid = session_id(); 
		if (User::login($user)) {
			setcookie("user_id" , $user->id, 0);
			setcookie("user_sid", $user->sid, 0);
		}
	}

	public static function logout() {
		setcookie('user_id',  '', time() - 3600*24*31*12 - 1);
		setcookie('user_sid', '', time() - 3600*24*31*12 - 1);
	}

	public static function isLoggedIn() {
		$loggedIn = false;
		if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_sid'])) {
			$filter = User::newFilter(array("User" => "*"));
			$filter->setFrom(array("User" => "u"));
			$filter->setWhere(array(
					'User.id'  => $_COOKIE['user_id'],
					'User.sid' => $_COOKIE['user_sid']));
			$filter->setLimit(1);
			if ($user = current(User::find($filter))) {
				$loggedIn = true;
			} else {
				Login::logout();
			}
		}
		return $loggedIn;
	}

	public static function loggedId() {
		return $_COOKIE['user_id'];
	}

	/**
	 * Logs user in and redirects to Config::BASE.
	 *
	 * @return void
	 */
	public static function checkLogin() {
		session_start();
		if (isset($_REQUEST['login']) && isset($_POST['user'])) {//if user logs in
			$filter = User::newFilter(array('User' => '*'));
			$filter->setLimit(1);
			$filter->setWhere(array(
						'User.login' => $_POST['user']['login'],
						'User.phash'  => Crypt::genPhash($_POST['user']['password']),
						'User.active'=> 1));
			if (!($user = current(User::find($filter)))) {
				Logger::err('LOGIN_FAIL', t("Login failed!"));
			} else {
				self::logUserIn($user);
				Logger::info(t("Connected!"));
			}
			Request::r2b();
		}
		if (isset($_REQUEST['logout'])) {
			self::logout();
			Logger::info(t("Disconnected!"));
			Request::r2b();
		}
	}

	public static function createUser($login, $password, $email) {
		$user_id = false;
		if ($err = self::badLogin($login)) {
			$_SESSION['user']['login'] = '' ;
			Logger::err('BAD_LOGIN', t("User name")."'" . htmlspecialchars($login)."' ".t("not allowed!"));
		} elseif ($err = User::existsBy(array('login' => $login))) {
			$_SESSION['user']['login'] = '' ;
			Logger::err('INUSE_LOGIN', t("User name")." '".htmlspecialchars($login)."' ".t("already registered. Choose other name!"));
		} elseif ($err = self::badPassword($password)) {
			Logger::err('BAD_PASS', t("Password")." '".htmlspecialchars($password)."' ".t("unsuitable"));
		} elseif ($err = self::badEmail($email)) {
			$_SESSION['user']['email'] = '' ;
			Logger::err('BAD_EMAIL', t("E-mail")." '" . htmlspecialchars($email)."' ".t("not allowed!"));
		} elseif ($err = User::existsBy(array('email' => $email))) {
			$_SESSION['user']['email'] = '' ;
			Logger::err('INUSE_EMAIL', t("Address")." ".htmlspecialchars($email)." ".t(" already registered. Provide another!"));
		} 
		if (!$err) {
			unset($_SESSION['user']);//clear
			$user = User::fromForm(array(
						'login' => $login,
						'email' => $email,
						'phash' => Crypt::genPhash($password),
						'aid'   => Crypt::genAid()));
			if ($user_id = $user->insert()) {  
				self::sendActivation($user_id);
			} else {
				Logger::err("NEW_USER_FAIL", t("User not created!"));
			}
		}
		return $user_id;
	}

	private static function sendActivation($id) {
		if ($user = User::load($id, array('id', 'aid'))) {
			$subject    = t("Activation");
			$message    = "?activate&id=".urlencode($user->id)."&aid=".urlencode($user->aid);
			$headers    = "From: localhost@example.com" . "\r\n".
						  "X-Mailer: PHP/".phpversion();

			echo "Activation by e-mail not enabled. Use provided link! ";
			echo('<a href="'.Request::base().$message.'">Click this link to activate your user account!</a>');exit;
			/*
			if (!empty($user->email)) { 
				mail($user->email, $subject, wordwrap($message,70));
			}*/
		}
	}

	private static function badPassword($pass) {
		return empty($pass);
	}

	private static function badEmail($email) {
		return empty($email);
	}

	private static function badLogin($login) {
		return empty($login);
	}

}

