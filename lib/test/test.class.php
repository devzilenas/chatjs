<?

class Test {

	private function t_methods() {
		return preg_grep("/^test_/", get_class_methods(get_called_class()));
	}

	public function run() {
		$test_methods = self::t_methods();
		foreach($test_methods as $method) {
			echo "Calling ".get_called_class()."::$method".'<br />';
			call_user_func("static::".$method);
		}
	}

}


