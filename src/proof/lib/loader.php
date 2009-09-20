<?php
/*
 * Created on 		19.09.2009
 *
 * @see http://github.com/swwwfactory/entry8
 *
 */

namespace swwwfactory\Dev;

const BSLASH = '\\';

class Pkg {

	private static $_mtab = array();
	private static $_rtab = array();
	private static $_base;

	public static $verbose = false;

	public static function autoload($aname) {
		$cname = $aname;
		try {
			self::_msg("try autoload class:[$aname]");
			$rtab = & self::$_rtab;
			if (isset($rtab[$cname])) {

				//
				//	extract format:
				//
				//	v_0	module full|relative path
				//	v_1	path base if relpath or null
				//	V_2 true if shared???
				//
				\extract($rtab[$cname], \EXTR_PREFIX_ALL, 'v');
				include $v_0;
				self::_msg('sub.module opened');
				return true;
			} else {
				$mtab = & self::$_mtab;
				if (isset($mtab[$cname])) {
					$defs = $mtab[$cname];
					\extract($defs, \EXTR_PREFIX_ALL, 'v');
					include $v_0;
					self::_msg('module opened with :mtab: strategy');
					return true;
				} else {
					$part = strtr($aname, BSLASH, '/');
					self::_msg("translated class [$aname] to [$part]");
					$cpath = self::$_base . $part . '.class.php';
					include $cpath;
					self::_msg('module opened with :base: strategy');
					return true;
				}
			}
		} catch (\Exception $e) {
			self::_msg('Autoload catch exception: ' . $e->getMessage());
			self::_dump($e);
		}
		return false;
	}

	public static function import($cname, $fname = null, $base = null){
		$mtab = & self::$_mtab;
		self::_msg("Import [$cname]");

		if (isset($mtab[$cname])) {
			self::_msg("Import: module [$cname] already defined");
			return true;
		} else {
			$mtab[$cname] = array($fname, $base);
			//
			//@todo spl push?
			//
		}
		$ret = \class_exists($cname, true);
		return $ret;
	}

	public static function init(){
		self::$_base = \My\Tests\AUTOLOADER_PKG_BASE;
		\spl_autoload_register(__NAMESPACE__ . '\Pkg::autoload');
	}

	public static function register($a){
		$rtab = & self::$_rtab;
		$rtab += $a;
	}

	public static function dump(){
		$a['rtab'] = self::$_rtab;
		$a['mtab'] = self::$_mtab;
		self::_dump($a);
	}

	private static function _msg($x){
		if (self::$verbose) namespace \Console::out($x);
	}

	private static function _dump($x){
		if (self::$verbose) namespace \Console::dump($x);
	}
}

namespace \Pkg::init();
?>