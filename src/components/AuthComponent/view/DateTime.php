<?php

namespace AuthComponent\View;

class DateTime {

	public function getHTMLString () : string {
		date_default_timezone_set("Europe/Stockholm");
		$day = date("l");
		$date = date("jS \of F Y");
		$time = date("H:i:s");
		$timeString = "{$day}, the {$date}, The time is ${time}";

		return '<p id="datetime">' . $timeString . '</p>';
	}
}

?>