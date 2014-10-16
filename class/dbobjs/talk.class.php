<?

class Talk extends Dbobj {
	protected static $FIELDS = array(
		'id'         => '%d',
		'talker_id'  => '%d',
		'created_on' => '%s', //datetime
		'what'       => '%s');

}

