<?

class LoggerHtmlBlock {
	# --------------------- LOGGER ---------------
	public static function messages() {
		$val = '';
		while ($error = Logger::nextErr()) {
			$val .= '<li class="error">'.so($error->msg).'</li>';
		}
		while ($info = Logger::nextInfo()) {
			$val .= '<li class="info">'.so($info->msg).'</li>';
		}
		if (!empty($val)) echo '<div id="messages"><ul>'.$val.'</ul></div>';
	}
}

