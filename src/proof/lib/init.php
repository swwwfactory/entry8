<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */

namespace swwwfactory\Dev;

class Main {

	public static function init(){
		//called
		include __DIR__ . '/utils.php';
		include __DIR__ . '/loader.php';
	}

	public static function setup(){
		//setup
		echo 'Called phar stub with method: ', __METHOD__, PHP_EOL;
	}
}

Main::init();
?>