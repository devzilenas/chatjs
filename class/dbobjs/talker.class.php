<?

/**
 * Talker
 */
class Talker extends Dbobj {

	protected static $FIELDS = array(
		'id'       => '%d',
		'nickname' => '%s',
		'user_id'  => '%d');

	public function beforeInsert() {
		return $this->canCreate();
	}

	public static function serializeableFields() {
		return array('id', 'nickname');
	}

	/**
	 * Talker must have nickname.
	 *
	 * @return array Validation information.
	 */
	public function hasValidationErrors() {
		$validation = array();
		if($v = self::validateNotEmpty('nickname')) $validation['nickname'] = $v;
		return $validation;
	}

	/**
	 * Checks whether Talker can be created.
	 *
	 * @return boolean
	 */ 
	public function canCreate() {
		// Talker nickname must be unique.
		return !self::existsBy(array('nickname' => $this->nickname)); 
	}

	public function loadByUserId($user_id) {
		return self::loadBy(array(self::cl('user_id') => $user_id));
	}

	/**
	 * Online talkers.
	 *
	 * @todo: limit users to online only.
	 *
	 * @return array
	 */
	public static function online() {
		$filter = self::newFilter(array("Talker" => "*"));
		$filter->setFrom(array("Talker" => "t"));
		return self::find($filter);
	}

	/**
	 * Set nickname for the Talker.
	 *
	 * @param integer $id Id of the talker.
	 * 
	 * @param string $nickname Nickname of the talker.
	 *
	 * @return void
	 */
	public function change_nickname($nickname) {
		if(!$this->isNew() && !self::existsBy(array('nickname' => $nickname))) 
			self::update(
				$this->id,
				array('nickname'), 
		        array('nickname' => $nickname));
	}

}

