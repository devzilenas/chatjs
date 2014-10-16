<?

function pluralize($str) { 
	$ret = $str;
	if(substr($str, -1) != 's') $ret = $ret.'s';
	if('person' === strtolower($str)) $ret = 'people';
	return $ret;
}

function arrayV(array $arr, $field) {
	$ret = array();
	foreach($arr as $e) 
		if (isset($e->$field)) 
			$ret[] = $e->$field;
	return $ret;
}

function nykstuku_kalba($str) {
	$rplp = array(
			'r' => 'j', 'R' => 'J',
			'l' => 'j', 'L' => 'J');

	return strtr($str, $rplp);
}

function t($str) {
	$str = trim($str);
	return class_exists('Language') ? Language::t($str) : $str;
}

// safe output
function so($str) { return htmlspecialchars($str); }

function od($str, $ifempty) { return so(('' != $str) ? $str : $ifempty); }

# -------------- TEST ---------------------
function expected($value, $got, $msg) {
	if ($value !== $got) {
		echo "Expected value:".$value.PHP_EOL;
		echo "Got:".$got.PHP_EOL;
		echo $msg.PHP_EOL;
	}
}

function c2u ($str) {
	// replace 
	//  someCamelCase to some_camel_case
	//  SomeCamelCase to some_camel_case
	return strtolower(preg_replace('#((?<!^)[A-Z](?=[a-z]))#','_$1',$str));//look for [A-Z] that are not preceeded '(?<!)' by the start of the string symbol and that are followed by '(?=)'
}

