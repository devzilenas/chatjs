<?

/**
 * Class for app specific requests.
 */
class Req extends Request implements ReqInterface {

	public static function isApiSetTalkerNickname() {
		return self::isApi() && isset($_GET['talker']) && isset($_GET['set_nickname']) && isset($_POST['talker']);
	}

	public static function isApiCurrentTalker() {
		return self::isApi() && isset($_GET['talker']) && isset($_GET['current']);
	}

	public static function isApiTalkerSays() {
		return self::isApi() && isset($_GET['say']) && isset($_POST['talk']);
	}

	public static function isApiTalks() {
		return self::isApi() && isset($_GET['talks']);
	}

	public static function isApiTalkers() {
		return self::isApi() && isset($_GET['talkers']);
	}

	# PROCESS 
	# API

	/**
	 * Gives talker information.
	 *
	 * @return void
	 */
	private static function processApiTalker() {
		$talker = ChatManager::talker();
		$response = Response::successful();
		$response->add_jobjs(array($talker->to_jobj()));
		echo $response->to_jobj();
	}

	/**
	 * Sets talkers nickname.
	 *
	 * @return void
	 */
	private static function processApiSetTalkerNickname() {
		$nickname = Dbobj::e($_POST['talker']['nickname']);
		ChatManager::changeNickname($nickname);
		$response = Response::successful();
		echo $response->to_jobj();
	}

	/**
	 * Processes api request to get talks.
	 *
	 * @return void
	 */
	private static function processApiTalks() {
		$lastTalkId = NULL;
		if(isset($_GET['last_talk_id'])) {
			$lastTalkId = Req::get0('last_talk_id');
		}
		$tTalks = ChatManager::talks($lastTalkId);
		$tjobjs  = array();
		$mapping = array(
			'id'         => 'id',
			'nickname'   => 'talker_nickname',
			'what'       => 'what',
			'created_on' => 'created_on');
		foreach($tTalks as $talk) 
			$tjobjs[] = $talk->to_jobj($mapping);
		$response = Response::successful();
		$response->add_jobjs($tjobjs); 
		echo $response->to_jobj();
	}

	private static function processApiTalkers() {
		$tmp = ChatManager::talkers();
		$tjobjs = array();
		foreach($tmp as $talker) {
			$tjobjs[] = $talker->to_jobj();
		}
		$response = Response::successful();
		$response->add_jobjs($tjobjs);
		echo $response->to_jobj();
	}

	private static function processApiTalkerSays() {
		$talker = ChatManager::talker();
		$talk   = self::oFromForm("Talk", array('what'));
		ChatManager::say($talk, $talker);
		$response = Response::successful();
		echo $response->to_jobj();
	}

	# REDIRECTS 

	/**
	 * Redirects to Talker information form.
	 *
	 * @return void
	 */
	public static function redirect_to_new_talker() {
		if(!isset($_GET['talker']) && !isset($_GET['new'])) {
			Request::hlexit("?talker&new");
			exit;
		}
	}

	/**
	 * Processes requests.
	 *
	 * @param boolean $api Tells whether it is api call.
	 *
	 * @return void
	 */
	public static function process($api = FALSE) {

		self::setApi($api);

		if(self::isApi()) {
			if(self::isApiCurrentTalker()) {
				self::processApiTalker();
			}
			if(self::isApiSetTalkerNickname()) {
				self::processApiSetTalkerNickname();
			}
			if(self::isApiTalkerSays()) {
				self::processApiTalkerSays();
			}
			if(self::isApiTalks()) {
				self::processApiTalks();
			}
			if(self::isApiTalkers()) {
				self::processApiTalkers();
			}
		} else {
	# -----------------------------------------
	# -------------- USER ---------------------
	# -----------------------------------------

	# -------------- NEW ----------------------
			if (isset($_REQUEST['newuser']) && isset($_POST['user'])) {
				$user = $_POST['user'];
				$_SESSION['user'] = $user;
				if ($user['password'] == $user['password_confirm']) {
					if (Login::createUser($user['login'], $user['password'], $user['email'])) {
						Request::hlexit("./");
					} else {
						Logger::err("UNDEF", t("User not created. Error!"));
						Request::hlexit("?registration");
					}
				} else {
					Logger::err('PASS_MATCH', t("Passwords don't match!"));
					Request::hlexit("?registration");
				}
			}

	# -------------- ACTIVATE -----------------
			if (isset($_REQUEST['activate']) && isset($_REQUEST['id']) && isset($_REQUEST['aid'])) {
				if(User::activate($_REQUEST['id'], urldecode($_REQUEST['aid']))) {
					Logger::info(t("User activated!"));
				} else {
					Logger::undefErr(t("User not activated!"));
				}

				Request::hlexit("./");
			} 
		}

	}
}

