<?php
namespace PNPSystem;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\System;

class Api {
	/**
	 * @static
	 * @return void
	 */
	public static function Setup(){
		spl_autoload_register( array(__CLASS__,'AutoLoader') );
	}
	/**
	 * AutoLoader: System
	 *
	 * @param string $Class
	 * @return bool
	 */
	private static function AutoLoader( $Class ) {
		/**
		 * Check: Class matches loader
		 */
		if( strpos( $Class, __NAMESPACE__, 0 ) === 0 ) {
			/**
			 * Replace: __NAMESPACE__ -> Current directory
			 * Build: Real path to class file
			 */
			$ClassPath = realpath( str_replace( __NAMESPACE__, __DIR__, $Class ).'.php' );
			/**
			 * Check: File exists
			 */
			if( file_exists( $ClassPath ) ) {
				/**
				 * Load: Class file
				 */
				Event::Message( 'Load: '.$Class.' -> '.$ClassPath, __METHOD__,__LINE__ );
				require_once( $ClassPath );
				return true;
			}
		}
		return false;
	}
}
/**
 * Load API
 */
Api::Setup();
