<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */

namespace My\classes\B;

Use swwwfactory\Dev;

class Foo {

	public static function whoami(){
		Dev\Console::out('called:' . __METHOD__);
	}

}
?>