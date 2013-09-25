<?php

function safeInput($string) {
	$string = trim($string);
        $string = str_replace('!', '', $string);
        $string = stripslashes($string);
	$string = htmlspecialchars($string);
	return $string;
}