<?
include 'includes.php';
include 'lib\test\test.class.php';

class TestJson extends Test {
	public static function test_convert_from_array_to_json() {
		$tdata = array(
			'string1' => 'string',
			'number2' => 10.3,
			'false3'  => false,
			'true4'   => true,
			'null5'   => NULL);
		$jo = new Jobj('SomeClass', $tdata);

		$ds = '{"string1" : "string", "number2" : 10.3, "false3" : false, "true4" : true, "null5" : null}';

		expected($ds, $jo->to_s(), "Must convert to valid json data structure:  $ds ");
	}

	public static function test_convert_from_object_to_json() {
		$obj           = new Talker();
		$obj->id       = 1;
		$obj->nickname = 'Nickname1';

		$jo = $obj->to_jobj();

		$ds = '{"id" : 1, "nickname" : "Nickname1", "user_id" : null}';

		expected($ds, $jo->to_s(), "Must convert to valid json data structure:  $ds ");
	}

	public static function test_convert_array_of_objs_to_json() {
		$jobjs         = array();

		$obj           = new Talker();
		$obj->id       = 1;
		$obj->nickname = 'Nickname1';
		$jobjs[]       = $obj->to_jobj();

		$obj           = new Talker();
		$obj->id       = 2;
		$obj->nickname = 'Nickname2';
		$jobjs[]       = $obj->to_jobj(); 

		echo PHP_EOL;
		echo Json::jo2jc($jobjs, 'talkers');

		$ds = '{"id" : 1, "nickname" : "Nickname1", "user_id" : null}';

	}
}

//DB::test_connect();

class TestChart extends Test {
	public static function test_draw() { 
		$chart = new Chart(array(
			39.6, 36.8, 37.7, 36.9, 36.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.6, 37.8, 37.7, 36.9, 36.6, 36.7, 36.9), 600, 600, 35, 41, 36.6);
		echo '<img src="data:image/png;base64,'.$chart->toImage64().'" />';
	}
}

class TestSQL extends Test {

	private static function getDbObjFilter1($name) {
		$filter = $name::newFilter(array($name => '*'));
		$filter->setLimit(1); 
		return $filter;
	}

	private static function getDbObj($name) {
		return current($name::find(self::getDbObjFilter1($name)));
	}

	private static function getC() { return $DBOBJS[0]; }

# ------------ FIND ONLY ONE ----------- 
	public static function test_find_one_two_objects() {
		$name   = self::getC();
		$firsto = self::getDbObj($name);
		expected(1, count($firsto), 'Has to find only one '.$name);

		$filter->setLimit(2);
		$twoos  = $name::find($filter);
		expected(2, count($twoos), "Has to find two ".pluralize($name));
	}

	public static function test_find_with_attribute() {
		$name    = self::getC();

		$filtero = self::getDbObjFilter1($name);
		$filtero->setWhat(array($name => array($fieldl)));

		$fns     = $name::fieldNames();
		$fieldl  = $fns[0];
		$fieldnl = $fns[1];

		$o = current($name::find($filtero));
		expected(TRUE , isset($o->$fieldl), "Has to have loaded field ".$fieldl);
		expected(FALSE, isset($o->$fieldnl), "Should not have loaded field ".$fieldnl);
	}

	public static function test_exists() {
		$name = self::getC(); 
		$o    = self::getDbObj($name);
		expected(TRUE, $name::exists($o->id), "Has to find $name with id=$o->id"); 
	}

	public static function test_load() {
		$name = self::getC(); 
		$o    = self::getDbObj($name);
		$ol   = $name::load($o->id);
		expected($o->id, $ol->id, "Has to load one $name with id=$o->id. Got id=$ol->id");
	}
}

//TestJson::run();
//TestSQL::run();
//TestChart::run();
