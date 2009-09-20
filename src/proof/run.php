<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */

namespace My\Tests;

Use swwwfactory\Dev;

const AUTOLOADER_PKG_BASE 		= __DIR__;
const MY_PHAR_PKG_NAME 				= 'my.tests.phar';
const MY_LIB_PHAR_PKG_NAME 		= 'my.lib.phar';

include MY_LIB_PHAR_PKG_NAME;

Dev\Console::out();

include MY_PHAR_PKG_NAME;

Dev\Pkg::import('My\classes\foo\Bar\Module', 'phar://' . MY_PHAR_PKG_NAME . '/foo/Bar.php');
Dev\Pkg::import('My\classes\A\Helper', 'phar://' . MY_PHAR_PKG_NAME . '/A.php');

\My\classes\A\Helper::whoami();
\My\classes\foo\Bar\Module::whoami();

Dev\Console::out();

Dev\Console::out('Listing of ' . MY_LIB_PHAR_PKG_NAME . ':');
Dev\PharShell::ls(MY_LIB_PHAR_PKG_NAME);
Dev\Console::out();

Dev\Console::out('Listing of ' . MY_PHAR_PKG_NAME . ':');
Dev\PharShell::ls(MY_PHAR_PKG_NAME);
Dev\Console::out();

//Dev\Console::dump(\get_declared_classes());
Dev\Console::out('Success loading phar archives');
Dev\Console::out();
?>