<?

class Form {

	public static function submit($value) {
		return '<input type="submit" value="'.$value.'" />';
	}

	public static function inputHtml($type, $name, $value) {
		return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" />';
	}

	public static function hiddenInput($name, $value) {
		return self::inputHtml("hidden", $name, $value);
	}

	public static function actionUpdate() {
		return self::hiddenInput("action", "update");
	}

	public static function actionDelete() {
		return self::hiddenInput("action", "delete");
	}

	public static function optionsA($options, $selected) {
		$out = '';
		if (is_array($options))
			foreach($options as $key => $val) {
				$sel = ($val==$selected ? 'selected' : '');
				$out .= '<option '.$sel.' value="'.$val.'">'.$key.'</option>';
			}
		return $out;
	}

	public static function options($options, $selected) {
		$out = '';
		if (is_array($options))
			foreach($options as $opt) {
				$sel = ($opt==$selected ? 'selected' : '');
				$out .= "<option $sel value=\"$opt\">$opt</option>";
		}
		return $out;
	}
	
	public static function label($for, $txt) {
		return '<label for="'.$for.'">'.$txt.'</label>';
	}

	public static function validation($name, $field) {
		if($v = hasV($name, $field)) {
			unset($_SESSION[$name][$field]);
			return '<span class="error">'.$v.'</span>';
		}
	}

	public static function dateSel($time, $range = NULL, $name = 'date') {
		if(NULL === $range) $range = range(date("Y", $time) - 3, date("Y", $time) + 3);

		return '
			<select name="'.$name.'[Y]">
				'.self::options($range, date("Y", $time)).'
			</select> - 
			<select name="'.$name.'[m]">
				'.self::options(range(1,12), date("m", $time)).'
			</select> -
			<select name="'.$name.'[d]">
				'.self::options(range(1,31), date("d", $time)).'
			</select>';
	}

	public static function timeSel($time, $name = 'time') {
		return '
			<select name="'.$time.'[H]">
				'.self::options(range(0,23), date("H", $time)).'
			</select> : 
			<select name="'.$time.'[i]">
				'.self::options(range(0,59), date("i", $time)).'
			</select>';
	}

}

