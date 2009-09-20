<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */
namespace swwwfactory\Dev;

class Console {

	private static $_meth_dump;
	private static $_meth_out;

	public static function init(){
		if (isset($_SERVER['SHELL'])) {
			self::$_meth_out = function($x) {
				echo $x, PHP_EOL;
			};
			self::$_meth_dump = function($x) {
				print_r($x);
			};
		} else {
			self::$_meth_out = function($x) {
				echo $x, '<br>';
			};
			self::$_meth_dump = function($x) {
				echo '<pre>', print_r($x, true), '</pre>';
			};
		}
	}

	public static function out($x = ''){
		$_meth_out = self::$_meth_out;
		$_meth_out($x);
	}

	public static function dump($x){
		$_meth_dump = self::$_meth_dump;
		$_meth_dump($x);
	}
}

class ErrorHandler {

	public static function init(){
		$lvl = \E_ALL | \E_STRICT;
		$callBack = \sprintf('\%s::handleError', __CLASS__);
		\set_error_handler($callBack, $lvl);
		\error_reporting($lvl);
	}

	public static function handleError($errno, $errstr , $errfile = null, $errline = null, $errcontext = null){
		$e = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
		$e->errcontext = $errcontext;
		throw $e;
	}
}

class PharBuilder {

	public static function run($conf) {
		self::_build($conf);
	}

	protected static function _build($conf){
		$pathToPhar = $conf['path_to_phar'];
		$pharName = $conf['phar_name'];
		$srcPath = $conf['src_path'];

		$initModule = $conf['init_module'];
		$setup = $conf['setup'];

		$file = $pathToPhar . '/' . $pharName;
		if ($conf['delete_old_phar_onbuild'] && \file_exists($file)) \unlink($file);

		$falias = $pharName;
		$mapPhar = $pharName;

		$phar = new \Phar($file, 0, $falias);
		$phar->startBuffering();

		$dir = $phar->buildFromDirectory($srcPath);

		$stub = '<?php ' .
		        "\Phar::mapPhar('$falias'); " .
		        'include ' .
		        "'phar://$falias/{$initModule}'; " .
		        "{$setup}" .
		        '__HALT_COMPILER();';

		$phar->setStub($stub);

		//
		//
		if (isset($conf['compress_pkg']) && $conf['compress_pkg'] == 'GZ') $phar->compressFiles(\Phar::GZ);
		//
		//

		$phar->stopBuffering();
	}
}

class PharShell {

	public static function dump($fname){
		//
		//define filter function as callBack closure
		//
		$fnFilter = function($el) {
			$name = $el->name;
			return $name !== 'getContent' && \preg_match('/get.*/', $name) > 0;
		};

		$fn = function($fd, $key, $itm) use ($fnFilter) {
			$rc = new \ReflectionClass($itm);
			$ameth = $rc->getMethods();
			$info = array();
			foreach ($ameth as $methItm) {
				if ($fnFilter($methItm)) {
					$methName = $methItm->name;
					$itmName = substr($methName, 3);
					try {
						$info[$itmName] = $itm->$methName();
					} catch (\Exception $e){
						$info[$itmName] = 'N/A: ' . $e->getMessage();
					}
				}
			}
			$dump = compact('itm', 'key', 'info');
			Console::dump($dump);
			return false;
		};

		self::_iterate($fname, $fn);
	}

	public static function ls($fname){
		$instanceName = __CLASS__;
		$fn = function($fd, $key, $itm) use ($instanceName) {
			$t = '%s%4o %8X %-12s %-12s %14d [%s]';
			$a = $instanceName::translateItem($itm);
			$s = \sprintf($t,
				$a['type'], $a['perms'],
				 $a['crc32'], $a['mtime'], $a['ctime'],  $a['size'], $a['path']);

			Console::out($s);
			return false;
		};

		self::_iterate($fname, $fn);
	}

	public static function translateItem($itm){
		$tf = "%d-%m-%y.%H:%M:%S";
		$a['type'] = substr($itm->getType(), 0, 1);
		$a['owner'] = $itm->getOwner();
		$a['group'] = $itm->getGroup();
		$a['perms'] = $itm->getPerms();
		$a['size'] = $itm->getSize();
		$a['crc32'] = $itm->getCRC32();
		$a['mtime'] = strftime($tf, $itm->getMTime());
		$a['ctime'] = strftime($tf, $itm->getCTime());
		$a['atime'] = strftime($tf, $itm->getATime());
		$a['fname'] = $itm->getFilename();
		$a['path'] = $itm->getPathname();
		return $a;
	}

	private static function _iterate($fname, $fn){
		$fd = new \Phar($fname);
		foreach (new \RecursiveIteratorIterator($fd) as $key => $itm){
			if ($fn($fd, $key, $itm)) break;
		}
	}

}

Console::init();
ErrorHandler::init();
?>