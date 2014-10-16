<?

class Request {
	public static $m_api;

	public static function setApi($api) { 
		self::$m_api = $api;
	}
	public static function isApi() {
		return self::$m_api;
	}

	public static function base() {
		return Config::BASE;
	}

	/**
	 * Redirects to BASE
	 *
	 * @return void
	 */
	public static function r2b() {
		self::hlexit(self::base());
	}

	/**
	 * Checks whether is action.
	 *
	 * @param $name Action name
	 *
	 * @return boolean
	 */
	public static function isAction($name) {
		return isset($_POST['action']) && $name === $_POST['action'];
	}

	/**
	 * Checks whether is form submit to create a new object.
	 *
	 * @return boolean 
	 */
	public static function isCreate() {
		return isset($_POST['action']) && 'create' == $_POST['action'];
	}

	public static function get0($name) {
		return (!empty($_REQUEST[$name])) ? (int)$_REQUEST[$name] : 0;
	}

	public static function getNull($name) {
		return (!empty($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
	}

	public static function gPostArray($name) {
		return isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
	}

	public static function hlexit($header) {
		header("Location: ".$header);
		exit;
	}

	/**
	 * Saves 
	 *
	 * @param $name string Class name of the object wich data is saved.
	 * @param $name array Data to save.
	 *
	 * @return void
	 */
	static function saveToSession($name, array $value) {
		$_SESSION[c2u($name)] = $value;
	}

	/**
	 * Loads object from session and unsets it.
	 *
	 * @param $name string Name of the Class.
	 *
	 * @return array|null
	 */
	protected static function loadFromSessionU($name) {
		if(isset($_SESSION[$name])) {
			$n   = c2u($name);
			$ret = $_SESSION[$n];
			unset($_SESSION[$n]);
			return $ret;
		}
	}


	/**
	 * Saves validation data into session.
	 *
	 * @param $class string Name of the class to which object belongs.
	 * @param $validation array Validation information.
	 *
	 * @return void
	 */
	protected static function saveValidation($class, array $validation) {
		$_SESSION[c2u($class).'_validation'] = $validation;
	}

	/**
	 * Loads object data from POST.
	 *
	 * @param $class string Class name of the object.
	 *
	 * @param $fields array Field names to load data from.
	 * 
	 * @return object Object of the class data.
	 */
	protected static function oFromForm($class, array $fields) {
		$cl = c2u($class);
		if(isset($_POST[$cl])) {
			$o = $class::fromForm($_POST[$cl], $fields);
			return $o;
		}
	}

}

