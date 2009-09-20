<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */

namespace My\classes\foo\Bar;

Use swwwfactory\Dev;

class Module {

	public static function __callStatic($methName, $args){
		if ($methName == 'whoami') {
			Dev\Console::out('whoami:' . __CLASS__);
		} else {
			throw new \Exception(sprintf("Undefine method [%s] in class [%s]", $methName, __CLASS__));
		}
	}
}
?>