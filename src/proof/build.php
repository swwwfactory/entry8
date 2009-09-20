<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 */

namespace My\Tests;

const AUTOLOADER_PKG_BASE 		= __DIR__;
const MY_PHAR_PKG_NAME 				= 'my.tests.phar';
const MY_LIB_PHAR_PKG_NAME 		= 'my.lib.phar';
const DELETE_OLD_PHAR_ONBUILD = true;

include __DIR__ . '/lib/utils.php';
include __DIR__ . '/lib/loader.php';

Use swwwfactory\Dev;

class Main {

	private static $_classes_stack;

	public static function run(){
		self::_init();
		self::_buildMy(); self::_testMyPackage(); self::_testImportModuleFromPackage();
		self::_buildLibExample();
		self::_showNewClasses();
	}

	private static function _init(){
		$stack = & self::$_classes_stack; $stack = new \SplStack();
		$stack->push(\get_declared_classes());
	}

	private static function _newConf(){
		$conf['delete_old_phar_onbuild']	= DELETE_OLD_PHAR_ONBUILD;
		//$conf['compress_pkg']	= 'GZ';
		$conf['path_to_phar']	= __DIR__;
		$conf['init_module']	= '/init.php';
		return $conf;
	}

	private static function _buildMy(){
		$conf = self::_newConf();
		$conf['src_path']			= __DIR__ . '/classes';
		$conf['phar_name']		= MY_PHAR_PKG_NAME;
		$conf['setup']				=<<<'EOD'
\My\classes\Init\Main::setup();
EOD;

		Dev\PharBuilder::run($conf);
	}

	private static function _buildLibExample(){
		$conf = self::_newConf();
		$conf['src_path']			= __DIR__ . '/lib';
		$conf['phar_name']		= MY_LIB_PHAR_PKG_NAME;
		$conf['setup']				=<<<'EOD'
/* no setup code */
EOD;

		Dev\PharBuilder::run($conf);
	}

	private static function _testMyPackage(){
		include MY_PHAR_PKG_NAME;
		include 'phar://' . MY_PHAR_PKG_NAME . '/A.php';
	}

	private static function _testImportModuleFromPackage(){
		Dev\Pkg::import('My\classes\foo\Bar\Module', 'phar://' . MY_PHAR_PKG_NAME . '/foo/Bar.php');
	}

	private static function _showNewClasses(){
		Dev\Console::out();
		Dev\Console::out('Opened classes from phar package:');
		Dev\Console::dump(\array_diff(\get_declared_classes(), self::$_classes_stack->pop()));
	}
}

Main::run();
Dev\Console::out();
Dev\Console::out('Success executing build.php');
Dev\Console::out();
?>