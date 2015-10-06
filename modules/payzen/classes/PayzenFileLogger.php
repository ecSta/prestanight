<?php
/**
 * PayZen payment module 1.2f (revision 61545)
 *
 * Compatible with V2 payment platform. Developped for Prestashop 1.5.0.x.
 * Support contact: support@payzen.eu.
 * 
 * Copyright (C) 2014 Lyra Network (http://www.lyra-network.com/) and contributors
 * 
 * 
 * NOTICE OF LICENSE
 *
 * This source file is licensed under the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

/**
 * Extend logger class to implement logging disable and avoid to check before every log operation
 */
class PayzenFileLogger extends FileLogger {
	protected $logsEnabled = false;
	
	public function __construct($logsEnabled, $level = self::INFO) {
		$this->logsEnabled = $logsEnabled;		
		parent::__construct($level);
	}
	
	/**
	 * log message only if logs are enabled
	 *
	 * @param string message
	 * @param level
	 */
	public function log($message, $level = self::DEBUG) {
		if(!$this->logsEnabled) {
			return;
		}
		
		parent::log($message, $level);
	}
}