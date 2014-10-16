<?

class LoginHtmlBlock {

	public static function hello($user) {
		return '<p>'.t("Hello").' <b>'.so($user->login).'</b></p>';
	}

# ----------- USER LOGIN ------------------
	public static function newUser() {
		if(isset($_SESSION['user'])) {
			$user = User::fromForm($_SESSION['user'], array('login', 'email'));
		} else {
			$user = new User();
		}
	return '<form action="?newuser" method="post">
			<label for="user_login">'.t("Login").'</label>
			<input class="zmogelis" type="text" name="user[login]" id="user_login" value="'.so($user->login).'" /><br />
			<label for="user[password]">'.t("Password").'</label>
			<input class="password" type="password" name="user[password]" id="user[password]" /><br />
			<label for="user[password_confirm]">'.t("Confirm password").'</label>
			<input class="password" type="password" name="user[password_confirm]" id="user[password_confirm]" /><br />
			<label for="user_email">'.t("E-mail").'</label>
			<input class="mail" type="text" name="user[email]" id="user_email" value="'.so($user->email).'" /><br />
			<input class="submit" type="submit" value="'.t("Create your account").'" /> <a href="?">'.t("Cancel").'</a></form>';
	}

	public static function loginForm() {
		return '
		<form action="?login" method="post">
		<label for="user_login">'.t("Login").'</label>
		<input class="zmogelis" type="text" name="user[login]" id="user_login" /><br /> 
		<label for="user_password">'.t("Password").'</label>
		<input class="password" type="password" name="user[password]" id="user_password" /><br />
		<input class="submit" type="submit" value="'.t("Login").'"> '.t("or").' <a href="?registration">'.t("Create your account").'</a>
		</form>';
	}

	public static function Login() {
		if (!Login::isLoggedIn()) {
			if(isset($_REQUEST['registration'])) {
				echo self::newUser();
			} else {
				echo self::loginForm();
			}
		}
	}
}

