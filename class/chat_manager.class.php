<?

class ChatManager {

	/**
	 * Changes nickname of talker.
	 * 
	 * @param string $nickname Nickname to set.
	 *
	 * @return void
	 */
	public static function changeNickname($nickname) {
		$talker = self::talker();
		$talker->change_nickname($nickname);
	}

	/**
	 * Talker talks in a chat.
	 *
	 * @param Talk $talk What to say.
	 *
	 * @param Talker $talker Who says.
	 */
	public static function say(Talk $talk, Talker $talker) {
		$talk->setDateTime('created_on', $_SERVER['REQUEST_TIME']);
		$talk->talker_id  = $talker->id;
		$talk->insert();
	}

	/**
	 * Returns chat talks.
	 *
	 * @param integer $lastTalkId Id of the last known talk .
	 *
	 * @param integer $limit Limits the number of talks to return from the chat.
	 *
	 * @return array|Talk
	 */
	public static function talks($lastTalkId = NULL, $limit = 100) {
		$filter = new SqlFilter("tk.*, tr.nickname");
		$filter->setFrom(Talk::tableName()." tk");
		$filter->setJoin("LEFT JOIN ".Talker::tableName()." tr");
		$filter->setOn("ON tk.talker_id = tr.id");
		if(NULL !== $lastTalkId) 
			$filter->setWhere("tk.id > ".Dbobj::eq($lastTalkId));
		$filter->setOrderBy("tk.id ASC");
		$filter->setLimit($limit);
		return Talk::find($filter);
	}

	/**
	 * Returns all online talkers
	 * 
	 * @return array
	 */
	public static function talkers() {
		return Talker::online();
	}

	/**
	 * Loads Talker data from database.
	 *
	 * @return Talker
	 */
	public static function talker() {
		return Talker::loadByUserId(Login::user()->id);
	}

	/**
	 * Checks if the user has talker information and creates talker for the user.
	 *
	 * @return void
	 */
	public static function checkTalker() {
		if(Login::isLoggedIn()) {
			if(!$talker = self::talker()) {
				$user     = Login::user();
				$nickname = $user->login; 
				// If nickname already in use, generate pseudo unique.
				if(Talker::existsBy(array('nickname' => $nickname))) { 
					Logger::undefErr(sprintf(t("Nickname %s in use, you can change it."), $nickname));
					$nickname .= Crypt::randomCode(1);
				}
				
				Talker::fromForm(
					array(
						'nickname' => $nickname,
						'user_id'  => $user->id),
					array('nickname', 'user_id'))->insert();
			}
		}
	}

}

