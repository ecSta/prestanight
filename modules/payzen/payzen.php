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

require_once dirname(__FILE__) . '/classes/payzen_api.php';
require_once dirname(__FILE__) . '/classes/PayzenFileLogger.php';
require_once dirname(__FILE__) . '/classes/admin/PayzenAdminDisplay.php';

/**
 * PayZen payment module main class.
 */
class Payzen extends PaymentModule {
 	const ON_FAILURE_RETRY = 'retry';
 	const ON_FAILURE_SAVE = 'save';
 	
 	const ORDER_ID_REGEX = '#^[a-zA-Z0-9]{1,9}$#';
 	const CUST_ID_REGEX = '#^[a-zA-Z0-9]{1,8}$#';
 	
 	const PRODUCT_REF_REGEX = '#^[a-zA-Z0-9]{1,64}$#';
 	const PRODUCT_LABEL_REGEX = '#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ ]{1,255}$#ui';
	
 	const DELIVERY_COMPANY_REGEX = '#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ /\'-]{1,127}$#ui';
 	
 	const TNT_RELAY_POINT_PREFIX = 'JD';
 	
	private $_multiLangFields = array('PAYZEN_STD_TITLE', 'PAYZEN_MULTI_TITLE', 'PAYZEN_ONEY_TITLE');
	private $_amountFields = array('PAYZEN_3DS_MIN_AMOUNT', 'PAYZEN_STD_AMOUNT_MIN', 'PAYZEN_STD_AMOUNT_MAX',
			'PAYZEN_MULTI_AMOUNT_MIN', 'PAYZEN_MULTI_AMOUNT_MAX'
	);
	private $_payzenApi = null;
	public $logger = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'payzen';
		$this->tab = 'payments_gateways';
		$this->version = '1.2f';
		$this->author = 'Lyra Network';
		$this->controllers = array('redirect', 'submit');
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';
		
		parent::__construct();
		
		$orderId = (int)(Tools::getValue('id_order', 0));
		$order = new Order($orderId);
		if(($order->module == $this->name) && (Tools::getValue('controller', '') == 'orderconfirmation')) {
			// patch to use different display name according to the used payment mode
			$this->displayName = $order->payment;
		} else {
			$this->displayName = 'PayZen';
		}
		
		$this->description = sprintf($this->l('Accept payments by credit cards with %s'), ' PayZen');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your module details ?');
		
		$this->logger = new PayzenFileLogger(Configuration::get('PAYZEN_ENABLE_LOGS') != 'False');
		$this->logger->setFilename(_PS_ROOT_DIR_.'/log/'.date('Y_m').'_payzen.log');
	}

	/**
	 * Return the list of configuration parameters with their payzen names and default values.
	 * 
	 * @return array[array[key, name, default]] 
	 */
	private function _getAdminParameters() {
		// NB : keys are 32 chars max
		
		return array(
				array('key' => 'PAYZEN_ENABLE_LOGS', 'default' => 'True', 'label' => 'Logs'),
				
				array('key' => 'PAYZEN_SITE_ID', 'name' => 'site_id', 'default' => '12345678', 'label' => 'Site id'),
				array('key' => 'PAYZEN_KEY_TEST', 'name' => 'key_test', 'default' => '1111111111111111', 'label' => 'Test certificate'),
				array('key' => 'PAYZEN_KEY_PROD', 'name' => 'key_prod', 'default' => '2222222222222222', 'label' => 'Production certificate'),
				array('key' => 'PAYZEN_MODE', 'name' => 'ctx_mode', 'default' => 'TEST', 'label' => 'Mode'),
				array('key' => 'PAYZEN_PLATFORM_URL', 'name' => 'platform_url', 'default' => 'https://secure.payzen.eu/vads-payment/', 'label' => 'Gateway URL'),
				
				array('key' => 'PAYZEN_DEFAULT_LANGUAGE', 'default' => 'fr', 'label' => 'Default language'),
				array('key' => 'PAYZEN_AVAILABLE_LANGUAGES', 'name' => 'available_languages', 'default' => '', 'label' => 'Available languages'),
				array('key' => 'PAYZEN_DELAY', 'name' => 'capture_delay', 'default' => '', 'label' => 'Delay'),
				array('key' => 'PAYZEN_VALIDATION_MODE', 'name' => 'validation_mode', 'default' => '', 'label' => 'Payment validation'),
				array('key' => 'PAYZEN_PAYMENT_CARDS', 'name' => 'payment_cards', 'default' => 'CB;VISA;VISA_ELECTRON;MASTERCARD;MAESTRO;E-CARTEBLEUE', 'label' => 'Available payment cards'),
				
				array('key' => 'PAYZEN_THEME_CONFIG', 'name' => 'theme_config', 'default' => '', 'label' => 'Theme configuration'),
				array('key' => 'PAYZEN_SHOP_NAME', 'name' => 'shop_name', 'default' => '', 'label' => 'Shop name'),
				array('key' => 'PAYZEN_SHOP_URL', 'name' => 'shop_url', 'default' => '', 'label' => 'Shop URL'),
				
				array('key' => 'PAYZEN_3DS_MIN_AMOUNT', 'default' => '', 'label' => 'Minimum amount for which activate 3DS'),
				
				array('key' => 'PAYZEN_REDIRECT_ENABLED', 'name' => 'redirect_enabled', 'default' => 'False', 'label' => 'Automatic redirection'),
				array('key' => 'PAYZEN_REDIRECT_SUCCESS_T', 'name' => 'redirect_success_timeout', 'default' => '5', 'label' => 'Success timeout'),
				array('key' => 'PAYZEN_REDIRECT_SUCCESS_M', 'name' => 'redirect_success_message', 'default' => 'Redirection vers la boutique dans quelques instants...', 'label' => 'Success message'),
				array('key' => 'PAYZEN_REDIRECT_ERROR_T', 'name' => 'redirect_error_timeout', 'default' => '5', 'label' => 'Failure timeout'),
				array('key' => 'PAYZEN_REDIRECT_ERROR_M', 'name' => 'redirect_error_message', 'default' => 'Redirection vers la boutique dans quelques instants...', 'label' => 'Failure message'),
				array('key' => 'PAYZEN_RETURN_MODE', 'name' => 'return_mode', 'default' => 'GET', 'label' => 'Return mode'),
				array('key' => 'PAYZEN_FAILURE_MANAGEMENT', 'default' => self::ON_FAILURE_RETRY, 'label' => 'Payment failed management'),
				array('key' => 'PAYZEN_RETURN_GET_PARAMS', 'name' => 'return_get_params', 'default' => '', 'label' => 'Additional GET parameters'),
				array('key' => 'PAYZEN_RETURN_POST_PARAMS', 'name' => 'return_post_params', 'default' => '', 'label' => 'Additional POST parameters'),
				
				array('key' => 'PAYZEN_STD_TITLE', 'default' => array('en' => 'Payment by bank card', 'fr' => 'Paiement par carte bancaire'), 'label' => 'Method title'),
				array('key' => 'PAYZEN_STD_ENABLED', 'default' => 'True', 'label' => 'Activation'),
				array('key' => 'PAYZEN_STD_AMOUNT_MIN', 'default' => '', 'label' => 'Minimum amount'),
				array('key' => 'PAYZEN_STD_AMOUNT_MAX', 'default' => '', 'label' => 'Maximum amount'),
				array('key' => 'PAYZEN_STD_CARD_DATA_MODE', 'default' => '1', 'label' => 'Card data entry mode'),
				
				array('key' => 'PAYZEN_MULTI_TITLE', 'default' => array('en' => 'Payment by bank card in several times', 'fr' => 'Paiement par carte bancaire en plusieurs fois'), 'label' => 'Method title'),
				array('key' => 'PAYZEN_MULTI_ENABLED', 'default' => 'False', 'label' => 'Activation'),
				array('key' => 'PAYZEN_MULTI_AMOUNT_MIN', 'default' => '', 'label' => 'Minimum amount'),
				array('key' => 'PAYZEN_MULTI_AMOUNT_MAX', 'default' => '', 'label' => 'Maximum amount'),
				array('key' => 'PAYZEN_MULTI_OPTIONS', 'default' => array(), 'label' => 'Payment options'),
				
				array('key' => 'PAYZEN_ONEY_TITLE', 'default' => array('en' => 'Payment with FacilyPay Oney', 'fr' => 'Paiement avec FacilyPay Oney'), 'label' => 'Method title'),
				array('key' => 'PAYZEN_ONEY_ENABLED', 'default' => 'False', 'label' => 'Activation'),
				array('key' => 'PAYZEN_ONEY_AMOUNT_MIN', 'default' => '', 'label' => 'Minimum amount'),
				array('key' => 'PAYZEN_ONEY_AMOUNT_MAX', 'default' => '', 'label' => 'Maximum amount'),
				array('key' => 'PAYZEN_ONEY_SHIP_OPTIONS', 'default' => array(), 'label' => 'Shipping options'),
				array('key' => 'PAYZEN_ONEY_PROD_CAT', 'default' => array(), 'label' => 'Product categories options'),
				array('key' => 'PAYZEN_ONEY_CAT_MODE', 'default' => '0', 'label' => ''),
				array('key' => 'PAYZEN_ONEY_COM_CAT', 'default' => 'FOOD_AND_GROCERY', 'label' => '')
		);
	}

	/**
	 * Returns a new PayzenMultiApi object loaded with the module configuration
	 * @return PayzenMultiApi
	 */
	public function getLoadedApi() {
		if($this->_payzenApi == null) {
			$this->_payzenApi = new PayzenMultiApi();
			$this->_payzenApi->set('version', 'V2');
			$this->_payzenApi->set('contrib', 'Prestashop1.5.0.x_1.2f/' . _PS_VERSION_);
			
			foreach ($this->_getAdminParameters() as $param) {
				if(key_exists('name', $param) && isset($param['name'])) {
					// only set payzen payment params
					$this->_payzenApi->set($param['name'], Configuration::get($param['key']));
				}
			}
		}
		
		return $this->_payzenApi;
	}
	
	/**
	 * @see PaymentModuleCore::install()
	 */
	public function install() {
		if (!method_exists('Tools', 'version_compare') || Tools::version_compare(_PS_VERSION_, '1.5')) {
			// incompatible version of Prestashop
			return false;
		}
		
		if (!parent::install() || !$this->registerHook('header') || !$this->registerHook('displayBackOfficeHeader')
		 	|| !$this->registerHook('payment') || !$this->registerHook('paymentReturn') || !$this->registerHook('displayShoppingCart')) {
			return false;
		}
		
		foreach ($this->_getAdminParameters() as $param) {
			if(in_array($param['key'], $this->_multiLangFields)) {
				// multilingual field, use prestashop IDs as keys
				$default = array();
					
				foreach (Language::getLanguages(false) as $language) {
					$default[$language['id_lang']] = key_exists($language['iso_code'], $param['default']) ? $param['default'][$language['iso_code']] : '';
				}
			} else {
				$default = $param['default'];
			}
			
			if (!Configuration::updateValue($param['key'], $default, false, false, false)) {
				return false;
			}
		}

		###ONEY_STATE_START###
		if(Configuration::get('PAYZEN_ONEY_PENDING')) {
			// rename oney status 
			Configuration::updateValue('PAYZEN_OS_ONEY_PENDING', Configuration::get('PAYZEN_ONEY_PENDING'));
			Configuration::deleteByName('PAYZEN_ONEY_PENDING');
		}

		// Oney payment pending confirmation order status
		if(!Configuration::get('PAYZEN_OS_ONEY_PENDING')) {
			// create a pending order status
			$lang = array (
					'en' => 'Funding request in progress',
					'fr' => 'Demande de financement en cours',
			);
			
			$name = array();
			foreach (Language::getLanguages(true) as $language) {
				$name[$language['id_lang']] = key_exists($language['iso_code'], $lang) ? $lang[$language['iso_code']] : '';
			}
		
			$oneyState = new OrderState();
			$oneyState->name = $name;
			$oneyState->invoice = false;
			$oneyState->send_email = false;
			$oneyState->module_name = $this->name;
			$oneyState->color = '#FF8C00';
			$oneyState->unremovable = true;
			$oneyState->hidden = false;
			$oneyState->logable = false;
			$oneyState->delivery = false;
			$oneyState->shipped = false;
			$oneyState->paid = false;
			
			if (!$oneyState->save() || !Configuration::updateValue('PAYZEN_OS_ONEY_PENDING', $oneyState->id)) {
				return false;
			}
			
			// add small icon to status
			@copy(_PS_MODULE_DIR_ . 'payzen/views/images/os_oney.gif', _PS_IMG_DIR_ . 'os/' . Configuration::get('PAYZEN_OS_ONEY_PENDING') . '.gif');
		}
		
		if(!Configuration::get('PAYZEN_OS_PAYMENT_OUTOFSTOCK')) {
			// create a pending order status
			$lang = array (
					'en' => 'Payment accepted on backorder',
					'fr' => 'Paiement accepté en attente de réapprovisionnement',
			);
				
			$name = array();
			foreach (Language::getLanguages(true) as $language) {
				$name[$language['id_lang']] = key_exists($language['iso_code'], $lang) ? $lang[$language['iso_code']] : '';
			}
		
			$oosState = new OrderState();
			$oosState->name = $name;
			$oosState->invoice = true;
			$oosState->send_email = true;
			$oosState->module_name = $this->name;
			$oosState->color = 'HotPink';
			$oosState->unremovable = true;
			$oosState->hidden = false;
			$oosState->logable = false;
			$oosState->delivery = false;
			$oosState->shipped = false;
			$oosState->paid = true;
			$oosState->template = 'outofstock';
				
			if (!$oosState->save() || !Configuration::updateValue('PAYZEN_OS_PAYMENT_OUTOFSTOCK', $oosState->id)) {
				return false;
			}
				
			// add small icon to status
			@copy(_PS_MODULE_DIR_ . 'payzen/views/images/os_oos.gif', _PS_IMG_DIR_ . 'os/' . Configuration::get('PAYZEN_OS_PAYMENT_OUTOFSTOCK') . '.gif');
		}
		###ONEY_STATE_END###
		
		return true;
	}

	/**
	 * @see PaymentModuleCore::uninstall()
	 */
	public function uninstall() {
		$result = true;
		foreach ($this->_getAdminParameters() as $param) {
			$result &= Configuration::deleteByName($param['key']);
		}

		// delete all obsolete payzen params but custom order states
		$result &= Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."configuration` WHERE `name` LIKE 'PAYZEN_%' AND `name` NOT LIKE 'PAYZEN_OS_%'");
		
		return $result && parent::uninstall();
	}

	/**
	 * Admin form management
	 * @return string
	 */
	public function getContent() {
		$msg = '';
		
		if (Tools::isSubmit('payzen_submit_admin_form')) {
			$this->_postProcess();
			
			if (!count($this->_errors)) {
				// no error, display update ok message
				$msg .= $this->displayConfirmation($this->l('Settings updated.'));
			} else {
				// display errors
				$msg .= $this->displayError(implode('<br />', $this->_errors));
			}
			
			$msg .= '<br />';
		}
		
		return $msg . $this->_renderForm();
	}

	/**
	 * Validate and save module admin parameters
	 */
	private function _postProcess() {
		$api = new PayzenApi(); // new instance of PayzenApi for parameters validation
		
		// load and validate from request
		foreach ($this->_getAdminParameters() as $param) {
			$key = $param['key'];
			$label = $this->l($param['label'], 'payzenadmindisplay', null);
			
			$value = Tools::getValue($key, null);
			
			if(in_array($key, $this->_multiLangFields)) {
				$value = array();
					
				foreach (Language::getLanguages(false) as $language) {
					$value[$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang'], '');
				}
			} elseif($key === 'PAYZEN_MULTI_OPTIONS') {
				if (!is_array($value) || empty($value)) {
					$value = array();
				} else {
					$error = false;	
					foreach ($value as $opt => $option) {
						if (!$option['label']
								|| !is_numeric($option['count'])
								|| !is_numeric($option['period'])
								|| ($option['first'] && (!is_numeric($option['first']) || $option['first'] < 0 || $option['first'] > 100))) {
							
							unset($value[$opt]); // not save this option
							$error = true;
						}
					}
					
					if($error) {
						$this->_errors[] = $this->l('One or more values are invalid for field "Payment options". Only valid lines are saved.');
					}
				}

				$value = serialize($value);
			} elseif($key === 'PAYZEN_AVAILABLE_LANGUAGES' || $key === 'PAYZEN_PAYMENT_CARDS') {
				$value = (is_array($value) && count($value) > 0) ? implode(';', $value) : '';
				
				if($value == '' && $key === 'PAYZEN_PAYMENT_CARDS') {
					// empty values for PAYZEN_PAYMENT_CARDS are not allowed
					$this->_errors[] = $this->l('You must select at least one card type.');
					continue;
				}
			} elseif($key === 'PAYZEN_ONEY_SHIP_OPTIONS') {
				if (!is_array($value) || empty($value)) {
					$value = array();
				} else {
					foreach ($value as $id => $option) {
						$carrier = $option['carrier_label'] . ($option['address'] ?  ' ' . $option['address'] : '');
						
						if (!preg_match(self::DELIVERY_COMPANY_REGEX, $carrier)) {	
							unset($value[$id]['address']); // not save this option
							$this->_errors[] = sprintf($this->l('Invalid value "%s" for field "%s".'), $option['carrier_label'] . ' ' . $option['address'], $label);
						}
					}
				}
				
				$value = serialize($value);
			} elseif($key === 'PAYZEN_ONEY_PROD_CAT') {
				if (!is_array($value) || empty($value)) {
					$value = array();
				}
				$value = serialize($value);
			} elseif(($key === 'PAYZEN_ONEY_ENABLED') && ($value == 'True')) {
				$oneyErrors = $this->_validateOney();
				if (is_array($oneyErrors) && !empty($oneyErrors)) {
					$this->_errors = array_merge($this->_errors, $oneyErrors);
					$value = 'False'; // there is errors, not allow Oney activation
				}
			} elseif(in_array($key, $this->_amountFields)) {
				if (!empty($value) && (!is_numeric($value) || $value < 0)) {
					$this->_errors[] = sprintf($this->l('Invalid value "%s" for field "%s".'), $value, $label);
					continue;
				}
			} elseif ($key === 'PAYZEN_STD_CARD_DATA_MODE' && $value == '3' && !$this->_checkSsl()) {
				$value = '1';
				$this->_errors[] = $this->l('The card data entry on merchant site cannot be used without enabling SSL.');
			}
			
			// validate with PayzenApi
			if(key_exists('name', $param) && isset($param['name']) && !$api->set($param['name'], $value)) {
				if(empty($value)) {
					$this->_errors[] = sprintf($this->l('The field "%s" is mandatory.'), $label);
				} else {
					$this->_errors[] = sprintf($this->l('Invalid value "%s" for field "%s".'), $value, $label);
				}
				
				continue;
			}
			
			// valid field : try to save into DB
			if (!Configuration::updateValue($key, $value)) {
				$this->_errors[] = sprintf($this->l('Problem occured while saving field "%s".'), $label);
			} else  {
				// temporary variable set to update PrestaShop cache
				Configuration::set($key, $value);
			}
		}
	}

	private function _validateOney() {
		$errors = array();
		if (Configuration::get('PS_ALLOW_MULTISHIPPING') === '1') {
			$errors[] = $this->l('Multishipping is activated. FacilyPay Oney payment cannot be enabled.');
		}
		
		$amountMin = Tools::getValue('PAYZEN_ONEY_AMOUNT_MIN', null);
		$amountMax = Tools::getValue('PAYZEN_ONEY_AMOUNT_MAX', null);
		if (empty($amountMin) || !is_numeric($amountMin) || $amountMin < 0 || empty($amountMax) || !is_numeric($amountMax) || $amountMax < 0) {
			$errors[] = $this->l('Please, enter minimum and maximum amounts in FacilyPay Oney payment tab as agreed with Banque Accord.');
		}
		
		return $errors;
	}
	
	private function _checkSsl() {
		return Configuration::get('PS_SSL_ENABLED') === '1';
	}
	
	private function _renderForm() {
        $display = new PayzenAdminDisplay($this);
		
		$this->context->smarty->assign(
			array(
				'payzen_request_uri'  => htmlentities($_SERVER['REQUEST_URI']),
		        'payzen_common'       => $display->commonHtml(),
				'payzen_general_tab'  => $display->generalTabHtml(),
				'payzen_single_tab'	  => $display->singleTabHtml(),
				'payzen_multi_tab'	  => $display->multiTabHtml(),
				'payzen_oney_tab'	  => $display->oneyTabHtml(),
				'tabs'		 		  => ($tab = (int)Tools::getValue('tabs')) ? $tab : '0',
			)
		);
		
		return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'payzen/views/templates/back/back_office.tpl');
	}

	/**
	 * Payment method selection page header.
	 * @param array $params
	 */
	public function hookHeader() {
		if (($this->context->controller instanceof OrderController && $this->context->controller->step == 3)
			|| $this->context->controller instanceof OrderOpcController) {
			
			$suffix = '';
			if(Tools::version_compare(_PS_VERSION_, '1.6')) {
				$suffix = '_1.5';
			}
			
			$this->context->controller->addCSS($this->_path . "views/css/payzen{$suffix}.css", 'all');
		}
	}
	
	/**
	 * Module admin configuration page header.
	 * @param array $params
	 */
	public function hookDisplayBackOfficeHeader() {
		if($this->context->controller instanceof AdminModulesController && (Tools::getValue('configure', '') == $this->name)) {
			$this->context->controller->addJS($this->_path . 'views/js/payzen.js');
			$this->context->controller->addJqueryPlugin('tabpane');

			if(Tools::version_compare(_PS_VERSION_, '1.6', '>=')) {
				// workaround to make multilingual fields working. TODO use Prestashop 1.6 language selection mode.
				return  '<style type="text/css">
							.language_flags {
							    background: none repeat scroll 0 0 #FFFFFF;
							    border: 1px solid #555555;
							    display: none;
							    float: left;
							    margin: 4px;
							    padding: 8px;
							    width: 80px;
							}
						</style>';
			}
		}
	}
	
 	/**
	 * Payment function, redirects the client to payment page
	 * @param array $params
	 * @return void|Ambigous <string, void, boolean, mixed, unknown>
	 */
	public function hookPayment($params) {
		/* @var $cart Cart */
		$cart = $this->context->cart;
		$cust = $this->context->customer;
		
		// currency support
		if (!$this->_checkCurrency()) {
			return;
		}
		
		$this->context->smarty->assign('back_compat', Tools::version_compare(_PS_VERSION_, '1.6'));
		
		$_html = '';
		
		if ($this->_checkStandardPayment()) {
 			$this->context->smarty->assign('payzen_std_title', Configuration::get('PAYZEN_STD_TITLE', $this->context->language->id));
			
			$cards = Configuration::get('PAYZEN_PAYMENT_CARDS');
			if(!empty($cards)) {
				$cards = explode(';', $cards);
			} else {
				// if no card type selected, display all supported cards 
				$cards = array_keys($this->getLoadedApi()->getSupportedCardTypes());
			}
			
			$this->context->smarty->assign('payzen_avail_cards', $cards);
			
			$entryMode = Configuration::get('PAYZEN_STD_CARD_DATA_MODE');
			if($entryMode === '3' && !$this->_checkSsl()) { // no data entry on merchant site without SSL
				$entryMode = '1'; 
			}
			$this->context->smarty->assign('payzen_std_card_data_mode', $entryMode);
			
			$_html .= $this->display(__FILE__, 'payment_std.tpl');
		}
		
		
		if ($this->_checkMultiPayment()) {
 			$this->context->smarty->assign('payzen_multi_title', Configuration::get('PAYZEN_MULTI_TITLE', $this->context->language->id));
			
			// multi payment options
			$this->context->smarty->assign('payzen_multi_options', $this->_getAvailableMultiPaymentOptions());
			$_html .= $this->display(__FILE__, 'payment_multi.tpl');
		}
		
		
		if ($this->_checkOneyPayment()) {
			$this->context->smarty->assign('payzen_oney_title', Configuration::get('PAYZEN_ONEY_TITLE', $this->context->language->id));
		
			// check address validity according to Oney payment specifications
			$billingAddress = new Address($cart->id_address_invoice);
			
			$deliveryAddress = new Address($cart->id_address_delivery);
			$colissimoAddress = $this->_getColissimoShippingAddress($cart, $deliveryAddress, $cust->id);
			if (is_a($colissimoAddress, 'Address')) {
				$deliveryAddress = $colissimoAddress;
			}
			
			$errors = $this->_checkAddressValidity($billingAddress, 'billing address');
			if(empty($errors)) {
				// billing address is valid, check delivery address
				$errors = $this->_checkAddressValidity($deliveryAddress, 'delivery address');
			}
			
			if (!empty($errors)) {
				$this->context->smarty->assign('payzen_oney_errors', $errors);
				$_html .= $this->display(__FILE__, 'payment_oney_errors.tpl');
			} else {
				$_html .= $this->display(__FILE__, 'payment_oney.tpl');
			}
		}
		
		return $_html;
	}
	
	private function _checkCurrency() {
		$cart = $this->context->cart;
		
		$cartCurrency = new Currency((int)$cart->id_currency);
		$currencies = $this->getCurrency((int)$cart->id_currency);
	
		if (!is_array($currencies) || !count($currencies)) {
			return false;
		}
	
		foreach ($currencies as $currency) {
			if ($cartCurrency->id == $currency['id_currency']) { // cart currency is allowed for this module
				return $this->getLoadedApi()->findCurrencyByAlphaCode($cartCurrency->iso_code) != null;
			}
		}
	
		return false;
	}
	
	private function _checkStandardPayment() {
		if (Configuration::get('PAYZEN_STD_ENABLED') != 'True') {
			return false;
		}
		
		$cart = $this->context->cart;
		
		// check amount restrictions
		$min = Configuration::get('PAYZEN_STD_AMOUNT_MIN');
		$max = Configuration::get('PAYZEN_STD_AMOUNT_MAX');
		if (($min != '' && $cart->getOrderTotal() < $min) || ($max != '' && $cart->getOrderTotal() > $max)) {
			return false;
		}
		
		return true;
	}
	
	private function _checkMultiPayment() {
		if(Configuration::get('PAYZEN_MULTI_ENABLED') != 'True') {
			return false;
		}
		
		$cart = $this->context->cart;
		
		// check amount restrictions
		$min = Configuration::get('PAYZEN_MULTI_AMOUNT_MIN');
		$max = Configuration::get('PAYZEN_MULTI_AMOUNT_MAX');
		if (($min != '' && $cart->getOrderTotal() < $min) || ($max != '' && $cart->getOrderTotal() > $max)) {
			return false;
		}
		
		// check available options
		if (!count($this->_getAvailableMultiPaymentOptions())) {
			return false;
		}
		
		return true;
	}
	
	private function _getAvailableMultiPaymentOptions() {
		// multi payment options
		$options = @unserialize(Configuration::get('PAYZEN_MULTI_OPTIONS'));
		if(!is_array($options) || !count($options)) {
			return array();
		}
		
		$cart = $this->context->cart;

		$enabledOptions = array();
		foreach ($options as $key => $option) {
			$min = $option['amount_min'];
			$max = $option['amount_max'];
				
			if (($min == '' || $cart->getOrderTotal() >= $min) && ($max == '' || $cart->getOrderTotal() <= $max)) {
				$enabledOptions[$key] = $option;
			}
		}
		
		return $enabledOptions;
	}
	
	private function _checkOneyPayment() {
		if(Configuration::get('PAYZEN_ONEY_ENABLED') != 'True') {
			return false;
		}
		
		// check multi shipping
		if(Configuration::get('PS_ALLOW_MULTISHIPPING') == '1') {
			return false;
		}
		
		$cart = $this->context->cart;
		
		// check amount restrictions
		$min = Configuration::get('PAYZEN_ONEY_AMOUNT_MIN');
		$max = Configuration::get('PAYZEN_ONEY_AMOUNT_MAX');
		if (($min != '' && $cart->getOrderTotal() < $min) || ($max != '' && $cart->getOrderTotal() > $max)) {
			return false;
		}
		
		// check order_id param
		if(!preg_match(self::ORDER_ID_REGEX, $cart->id)) {
			$msg = 'Order ID "%s" does not match FacilyPay Oney specifications. The regular expression for this field is %s. Module is not displayed.';
			$this->logger->logWarning(sprintf($msg, $cart->id, self::ORDER_ID_REGEX));
			return false;
		}
			
		// check cust_id param
		$cust = $this->context->customer;
		if(!preg_match(self::CUST_ID_REGEX, $cust->id)) {
			$msg = 'Customer ID "%s" does not match FacilyPay Oney specifications. The regular expression for this field is %s. Module is not displayed.';
			$this->logger->logWarning(sprintf($msg, $cust->id, self::CUST_ID_REGEX));
			return false;
		}
		
		// check products
		foreach ($cart->getProducts(true) as $product) {
			if(!preg_match(self::PRODUCT_REF_REGEX, $product['id_product'])) {
				// product id doesn't match FacilyPay Oney rules
				 
				$msg = 'Product reference "%s" does not match FacilyPay Oney specifications. The regular expression for this field is %s. Module is not displayed.';
				$this->logger->logWarning(sprintf($msg, $product['id_product'], self::PRODUCT_REF_REGEX));
				return false;
			} elseif(!preg_match(self::PRODUCT_LABEL_REGEX, $product['name'])) {
				// product label doesn't match FacilyPay Oney rules
				 
				$msg = 'Product label "%s" does not match FacilyPay Oney specifications. The regular expression for this field is %s. Module is not displayed.';
				$this->logger->logWarning(sprintf($msg, $product['name'], self::PRODUCT_LABEL_REGEX));
				return false;
			}
		}
		
		return true;
	}
	
	private function _checkAddressValidity($address, $addressType) {
		$invalidMsg = $this->l('The field %s of your %s is invalid.');
		$emptyMsg = $this->l('The field %s of your %s is mandatory.');
		
		$nameRegex = "#^[A-ZÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '-]{1,63}$#ui";
		$phoneRegex = "#^[0-9]{10}$#";
		$cityRegex = "#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '-]{1,127}$#ui";
		$streetRegex = "#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '.,-]{1,127}$#ui";
		$countryRegex = "#^FR$#i";
		$zipRegex = "#^[0-9]{5}$#";
		
		$errors = array();
		
		if(empty($address->lastname)) {
			$errors[] = sprintf($emptyMsg, $this->l('Last name'), $this->l($addressType));
		} elseif(!preg_match($nameRegex, $address->lastname)) {
			$errors[] = sprintf($invalidMsg, $this->l('Last name'), $this->l($addressType));
		}
			
		if(empty($address->firstname)) {
			$errors[] = sprintf($emptyMsg, $this->l('First name'), $this->l($addressType));
		} elseif(!preg_match($nameRegex, $address->firstname)) {
			$errors[] = sprintf($invalidMsg, $this->l('First name'), $this->l($addressType));
		}
			
		if(!empty($address->phone) && !preg_match($phoneRegex, $address->phone)) {
			$errors[] = sprintf($invalidMsg, $this->l('Phone'), $this->l($addressType));
		}
		
		if(!empty($address->phone_mobile) && !preg_match($phoneRegex, $address->phone_mobile)) {
			$errors[] = sprintf($invalidMsg, $this->l('Phone mobile'), $this->l($addressType));;
		}
			
		if(empty($address->address1)) {
			$errors[] = sprintf($emptyMsg, $this->l('Address'), $this->l($addressType));
		} elseif(!preg_match($streetRegex, $address->address1)) {
			$errors[] = sprintf($invalidMsg, $this->l('Address'), $this->l($addressType));
		}
			
		if(!empty($address->address2) && !preg_match($streetRegex, $address->address2)) {
			$errors[] = sprintf($invalidMsg, $this->l('Address2'), $this->l($addressType));
		}
			
		if(empty($address->postcode)) {
			$errors[] = sprintf($emptyMsg, $this->l('Zip code'), $this->l($addressType));
		} elseif(!preg_match($zipRegex, $address->postcode)) {
			$errors[] = sprintf($invalidMsg, $this->l('Zip code'), $this->l($addressType));
		}
			
		if(empty($address->city)) {
			$errors[] = sprintf($emptyMsg, $this->l('City'), $this->l($addressType));
		} elseif(!preg_match($cityRegex, $address->city)) {
			$errors[] = sprintf($invalidMsg, $this->l('City'), $this->l($addressType));
		}
			
		$country = new Country($address->id_country);
		if(empty($country->iso_code)) {
			$errors[] = sprintf($emptyMsg, $this->l('Country'), $this->l($addressType));
		} elseif(!preg_match($countryRegex, $country->iso_code)) {
			$errors[] = sprintf($invalidMsg, $this->l('Country'), $this->l($addressType));
		}
		
		return $errors;
	}
	
	
	/**
	 * Manage payement gateway response
	 * @param array $params
	 */
	public function hookPaymentReturn($params) {
		if (!$this->active || $params['objOrder']->module != $this->name) {
			return;
		}
		
		$error_msg = (Tools::getValue('error') == 'yes');
		
		$array = array(
				'check_url_warn' => (Tools::getValue('check_url_warn') == 'yes'),
				'maintenance_mode' => (Configuration::get('PS_SHOP_ENABLE') == '0'),
				'prod_info' => (Tools::getValue('prod_info') == 'yes'),
				'error_msg' => $error_msg
		);
		
		if ($error_msg === false) {
			$array['total_to_pay'] = Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false);
			$array['id_order'] = $params['objOrder']->id;
		}
		
		$this->context->smarty->assign($array);
		
		return $this->display(__FILE__, 'payment_return.tpl');
	}
	
	/**
	 * Before shopping cart display.
	 * @param array $params
	 */
	public function hookDisplayShoppingCart() {
		if(Tools::getValue('payzen_pay_error') == 'yes') {
			$this->context->controller->errors[] = $this->l('Your payment was not accepted. Please, try to re-order.');
			
			// unset HTTP_REFERER from smarty server variable to avoid back button display
			$server = $_SERVER;
			unset($_SERVER['HTTP_REFERER']);
			$this->context->smarty->assign('server', $_SERVER);
		}
	}
	
	// TODO to remove when So Colissimo fix cart delivery address id
	private function _getColissimoShippingAddress($cart, $psAddress, $idCustomer) {
		// So Colissimo not installed
		if(!Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
			return false;
		}
	
		// So Colissimo is not selected as shipping method
		if ($cart->id_carrier != Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
			return false;
		}
	
		// Get address saved by So Colissimo
		$return = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'socolissimo_delivery_info WHERE id_cart =\''.(int)($cart->id).'\' AND id_customer =\''.(int)($idCustomer).'\'');
		$newAddress = new Address();
	
		if (strtoupper($psAddress->lastname) != strtoupper($return['prname'])
			|| strtoupper($psAddress->firstname) != strtoupper($return['prfirstname'])
			|| strtoupper($psAddress->address1) != strtoupper($return['pradress3'])
			|| strtoupper($psAddress->address2) != strtoupper($return['pradress2'])
			|| strtoupper($psAddress->postcode) != strtoupper($return['przipcode'])
			|| strtoupper($psAddress->city) != strtoupper($return['prtown'])
			|| str_replace(array(' ', '.', '-', ',', ';', '+', '/', '\\', '+', '(', ')'),'',$psAddress->phone_mobile) != $return['cephonenumber']) {
				
			// Address is modified in So Colissimo page : use it as shipping address
			$newAddress->lastname = substr($return['prname'], 0, 32);
			$newAddress->firstname = substr($return['prfirstname'], 0, 32);
			$newAddress->postcode = $return['przipcode'];
			$newAddress->city = $return['prtown'];
			$newAddress->id_country = Country::getIdByName(null, 'france');
	
			if (!in_array($return['delivery_mode'], array('DOM', 'RDV'))) {
				$newAddress->address1 = $return['pradress1'];
				$newAddress->address1 .= isset($return['pradress2']) ?  ' ' . $return['pradress2'] : '';
				$newAddress->address1 .= isset($return['pradress3']) ?  ' ' . $return['pradress3'] : '';
				$newAddress->address1 .= isset($return['pradress4']) ?  ' ' . $return['pradress4'] : '';
			} else {
				$newAddress->address1 = $return['pradress3'];
				$newAddress->address2 = isset($return['pradress4']) ? $return['pradress4'] : '';
				$newAddress->other = isset($return['pradress1']) ?  $return['pradress1'] : '';
				$newAddress->other .= isset($return['pradress2']) ?  ' ' . $return['pradress2'] : '';
			}
				
			// Return the So Colissimo updated
			return $newAddress;
		} else {
			// Use initial address
			return false;
		}
	}
	
	/**
	* Generate form fields to post to the payment gateway.
	*/
	public function getFormFields($type='standard', $data=array()) {
		/* @var $cust Customer */
		/* @var $cart Cart */
		$cust = $this->context->customer;
		$cart = $this->context->cart;
		
		/* @var $billingCountry Address */
		$billingAddress = new Address($cart->id_address_invoice);
		$billingCountry = new Country($billingAddress->id_country);
			
		/* @var $deliveryAddress Address */
		$deliveryAddress = new Address($cart->id_address_delivery);
			
		// TODO to remove when So Colissimo fix cart delivery address id
		$colissimoAddress = $this->_getColissimoShippingAddress($cart, $deliveryAddress, $cust->id);
		if (is_a($colissimoAddress, 'Address')) {
			$deliveryAddress = $colissimoAddress;
		}
		$deliveryCountry = new Country($deliveryAddress->id_country);
		
		/* @var $api PayzenApi */
		$api = $this->getLoadedApi();
		
		/* detect default language */
		$language = strtolower(Language::getIsoById(intval($this->context->language->id)));
		if (!$api->isSupportedLanguage($language)) {
			$language = Configuration::get('PAYZEN_DEFAULT_LANGUAGE');
		}
		
		/* detect store currency */ 
		$cartCurrency = new Currency(intval($cart->id_currency));
		$currency = $api->findCurrencyByAlphaCode($cartCurrency->iso_code);
		
		/* Amount */
		$amount = $cart->getOrderTotal();
			
		$api->set('amount', $currency->convertAmountToInteger($amount));
		$api->set('currency', $currency->num);
			
		$api->set('cust_email', $cust->email);
		$api->set('cust_id', $cust->id);
		
		$custTitle = new Gender((int) ($cust->id_gender));
		$api->set('cust_title', $custTitle->name[Context::getContext()->language->id]);
		
		$api->set('cust_first_name', $billingAddress->firstname);
		$api->set('cust_last_name', $billingAddress->lastname);
		$api->set('cust_address', $billingAddress->address1 . ' ' . $billingAddress->address2);
		$api->set('cust_zip', $billingAddress->postcode);
		$api->set('cust_city', $billingAddress->city);
		$api->set('cust_phone', $billingAddress->phone);
		$api->set('cust_country', $billingCountry->iso_code);
		if ($billingAddress->id_state) {
			$state = new State((int) ($billingAddress->id_state));
			$api->set('cust_state', $state->iso_code);
		}
			
		$title = '';
		
		$this->logger->logInfo("Form data generation for cart #{$cart->id} with $type payment.");
		
		switch ($type) {
			case 'standard' : 
				// single payment card data
					
				if(key_exists('card_type', $data) && $data['card_type']) {
					// override payemnt_cards var
					$api->set('payment_cards', $data['card_type']);
				}
					
				if(key_exists('card_number', $data) && $data['card_number']) {
					$api->set('card_number', $data['card_number']);
					$api->set('cvv', $data['cvv']);
					$api->set('expiry_year', $data['expiry_year']);
					$api->set('expiry_month', $data['expiry_month']);
				
					// override action_mode to do a silent payment
					$api->set('action_mode', 'SILENT');
				}
					
				$title = Configuration::get('PAYZEN_STD_TITLE', $this->context->language->id);
				if(!$title) {
					$title = $this->l('Payment by bank card');
				}
				
				break;
				
			case 'multi' : 
				// multiple payment options
					
				$multiOptions = $this->_getAvailableMultiPaymentOptions();
				$option = $multiOptions[$data['opt']];
					
				$configFirst = $option['first'];
				$first = ($configFirst != '') ? $currency->convertAmountToInteger(($configFirst / 100) * $amount) : null;
				$api->setMultiPayment(null /* to use already set amount */, $first, $option['count'], $option['period']);
					
				// override cb contract
				$api->set('contracts', ($option['contract']) ? 'CB=' . $option['contract'] : null);
					
				$title = Configuration::get('PAYZEN_MULTI_TITLE', $this->context->language->id);
				if(!$title) {
					$title = $this->l('Payment by bank card in several times');
				}
				$title .= ' (' . $option['count'] . ' x)';
				
				break;
				
			case 'oney':
				// Oney payment
				
				// override with Oney payment cards
				$api->set('payment_cards', 'ONEY_SANDBOX;ONEY');
				
				$products = $cart->getProducts(true);
				
				if (Configuration::get('PAYZEN_ONEY_CAT_MODE') == '1') {
					$category = Configuration::get('PAYZEN_ONEY_COM_CAT');
				} else {
					$oneyCategories = @unserialize(Configuration::get('PAYZEN_ONEY_PROD_CAT'));
				}
				
				foreach ($products as $product) {
					if(!isset($category)) {
						// build query to get product default category
						$sql = 'SELECT `id_category_default` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product` = ' . (int)$product['id_product'];
						$dbCategory = Db::getInstance()->getValue($sql);
						
						$category = $oneyCategories[$dbCategory];
					}
					
					$api->addProductRequestField(
							$product['name'],
							$currency->convertAmountToInteger($product['total_wt']),
							$product['cart_quantity'],
							$product['id_product'],
							$category 
					);
				}
				
				// Oney delivery options defined in admin panel
				$oneyOptions = @unserialize(Configuration::get('PAYZEN_ONEY_SHIP_OPTIONS'));
				
				// retrieve carrier ID from cart
				if (isset($cart->id_carrier) && $cart->id_carrier > 0) {
					$carrierId = $cart->id_carrier;
				} else {
					$deliveryOptionList = $cart->getDeliveryOptionList();
						
					$deliveryOption = $cart->getDeliveryOption();
					$carrierKey = $deliveryOption[$deliveryAddress->id];
						
					foreach ($deliveryOptionList[$deliveryAddress->id][$carrierKey]['carrier_list'] as $id => $data) {
						$carrierId = $id;
						break;
					}
				}
				
				$shopNameRegexNotAllowed = "#[^A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ /'-]#ui";
				if($cart->isVirtualCart() || !isset($carrierId) || !is_array($oneyOptions) || empty($oneyOptions)) {
					// No shipping options
					$api->set('ship_to_type', 'ETICKET');
					$api->set('ship_to_speed', 'EXPRESS');
					
					$shop = Shop::getShop($cart->id_shop);
					$api->set('ship_to_delivery_company_name', preg_replace($shopNameRegexNotAllowed, ' ', $shop['name']));
				} elseif (Configuration::get('TNT_CARRIER_' . self::TNT_RELAY_POINT_PREFIX . '_ID') == $carrierId) {
						$api->set('ship_to_type', 'RELAY_POINT');
						$api->set('ship_to_speed', 'EXPRESS');
						
						if ($row = Db::getInstance()->getRow(
								"SELECT * FROM `" . _DB_PREFIX_ . "tnt_carrier_drop_off`
								WHERE `id_cart` = '" . $cart->id . "'"
							)) {
								$tntRelayAddress = $row['name'] . ' ' .$row['address'].' '. $row['zipcode']. ' '. $row['city'];
								$api->set('ship_to_delivery_company_name', preg_replace($shop_name_regex, ' ', $tntRelayAddress));
								$api->set('ship_to_last_name', preg_replace($shop_name_regex, ' ', $row['name']));
								$api->set('ship_to_street', preg_replace($shop_name_regex, ' ', $row['address']));
								$api->set('ship_to_zip', $row['zipcode']);
								$api->set('ship_to_city', preg_replace($shop_name_regex, ' ', $row['city']));
								$api->set('ship_to_country', 'FR');
						}
				} else {
					$deliveryType = $oneyOptions[$carrierId]['delivery_type'];
					$api->set('ship_to_type', $deliveryType);
					
					$companyName = $oneyOptions[$carrierId]['carrier_label'];
					if($deliveryType === 'RECLAIM_IN_SHOP'
						/* || $deliveryType === 'RELAY_POINT' || $deliveryType === 'RECLAIM_IN_STATION' TODO consider these cases */) {
						$companyName .= ' ' . $oneyOptions[$carrierId]['address'];	
					}
			
					$api->set('ship_to_delivery_company_name', $companyName);
					$api->set('ship_to_speed', $oneyOptions[$carrierId]['delivery_speed']);
				}
				
				$api->set('cust_status', 'PRIVATE'); // Prestashop don't manage customer type
				$api->set('ship_to_status', $deliveryAddress->company != '' ? 'COMPANY' : 'PRIVATE');
				
				$api->set('insurance_amount', ''); // not available in Prestashop by default
				$api->set('tax_amount', $currency->convertAmountToInteger($cart->getOrderTotal() - $cart->getOrderTotal(false)));
				$api->set('shipping_amount', $currency->convertAmountToInteger($cart->getTotalShippingCost()));

				$title = Configuration::get('PAYZEN_ONEY_TITLE', $this->context->language->id);
				if(!$title) {
					$title = $this->l('Payment with FacilyPay Oney');
				}
		}
		
		if($api->get('ship_to_type') == null || $api->get('ship_to_type') == 'PACKAGE_DELIVERY_COMPANY') {
			$api->set('ship_to_first_name', $deliveryAddress->firstname);
			$api->set('ship_to_last_name', $deliveryAddress->lastname);
			$api->set('ship_to_street', $deliveryAddress->address1);
			$api->set('ship_to_street2', $deliveryAddress->address2);
			$api->set('ship_to_zip', $deliveryAddress->postcode);
			$api->set('ship_to_city', $deliveryAddress->city);
			$api->set('ship_to_phone_num', $deliveryAddress->phone);
			$api->set('ship_to_country', $deliveryCountry->iso_code);
			
			if ($deliveryAddress->id_state) {
				$state = new State((int) ($deliveryAddress->id_state));
				$api->set('ship_to_state', $state->iso_code);
			}
		}
		
		$api->set('order_info', $title);		
		
		// activate 3ds ?
		$threedsMpi = null;
		if(Configuration::get('PAYZEN_3DS_MIN_AMOUNT') != '' && $amount < Configuration::get('PAYZEN_3DS_MIN_AMOUNT')) {
			$threedsMpi = '2';
		}
		$api->set('threeds_mpi', $threedsMpi);
		
		$api->set('language', $language);
		$api->set('order_id', $cart->id);
		$api->set('url_return', $this->context->link->getModuleLink($this->name, 'submit', array(), true));
		
		// patch to avoid signature error with HTML minifier introduced since Prestashop 1.6.0.3
		if(Configuration::get('PS_HTML_THEME_COMPRESSION') && Tools::version_compare(_PS_VERSION_, '1.6.0.3', '>=')) {			
			foreach ($api->getRequestFields() as $field) {
				$value = preg_replace('/\s+/m', ' ', $field->getValue());
				$api->set($field->getName(), $value);
			}
		}

		// prepare data for PayZen payment form
		return $api->getRequestFieldsArray();
	}
	
	/**
	* Save order and transaction info.
	*/
	public function saveOrder($cart, $orderStatus, $payzenResponse) {
		$this->logger->logInfo("Create order for cart #{$cart->id}.");
		
		// Retrieve customer from cart
		$customer = new Customer($cart->id_customer);
		
		// ps id_currency from currency iso num code
		$currencyId = Currency::getIdByIsoCodeNum((int)$payzenResponse->get('currency'));
		
		// 3ds extra message
		$msg3ds = "\n" . $this->l('3DS authentication : ');
		if ($payzenResponse->get('threeds_status') == 'Y') {
			$msg3ds .= $this->l('YES');
			$msg3ds .= "\n" . $this->l('3DS certificate : ') . $payzenResponse->get('threeds_cavv');
		} else {
			$msg3ds .= $this->l('NO');
		}
		
		// call payment module validateOrder
		$this->validateOrder(
			$cart->id, 
			$orderStatus,
			$payzenResponse->getFloatAmount(),
			$payzenResponse->get('order_info'), // title defined in admin panel and sent to platform as order_info
			$payzenResponse->getLogString() . $msg3ds,
			array(),		// $extraVars
			$currencyId,	// $currency_special
			false,			// $dont_touch_amount
			$customer->secure_key
		);
		
		// reload order
		$order = new Order((int)Order::getOrderByCartId($cart->id));
		
		$this->logger->logInfo("Order #{$order->id} created successfully for cart #{$cart->id}.");
		
		$this->savePayment($order, $payzenResponse);
		
		return $order;
	}
	
	/**
	 * Update current order state.
	 */
	public function setOrderState($order, $orderState, $payzenResponse) {
		$this->logger->logInfo("Payment status for cart {$order->id_cart} has changed. New order status is $orderState.");
		
		$msg = new Message();
		$msg->message = $payzenResponse->getLogString();
		$msg->id_order = intval($order->id);
		$msg->private = 1;
		$msg->add();
		
		$order->setCurrentState($orderState);
		$this->savePayment($order, $payzenResponse);		
	}
	
	/**
	 * Save payment information.
	 */
	public function savePayment($order, $payzenResponse) {
		$payments = $order->getOrderPayments();
		
		if(is_array($payments) && !empty($payments)) {
			foreach ($payments as $payment) {
				$payment->delete();
			}
			
			$order->total_paid_real = 0;
		}
		
		if(!$this->_isSuccessState($order)) {
			// no payment creation
			
			return;
		}
		
		// save transaction info
		$this->logger->logInfo("Save payment information for cart #{$order->id_cart}.");
		
		$invoices = $order->getInvoicesCollection();
		$invoice = count($invoices) > 0 ? $invoices[0] : null;
		
		$currency = $this->getLoadedApi()->findCurrencyByNumCode($payzenResponse->get('currency'));
		
		$paymentIds = array();
		if ($payzenResponse->get('card_brand') == 'MULTI') {
			$sequences = json_decode($payzenResponse->get('payment_seq'));
			$transactions = $sequences->transactions;
				 
			foreach($transactions as $trs) {
				$amount = $currency->convertAmountToFloat($trs->{'amount'});
	
				$transaction_id = $trs->{'sequence_number'} . '-' . $trs->{'trans_id'};
				if($trs->{'ext_trans_id'}) {
					$transaction_id .= '-'. $trs->{'ext_trans_id'};
				}
				
				if (!$order->addOrderPayment($amount, null, $transaction_id, null, null, $invoice)) {
					throw new PrestaShopException('Can\'t save Order Payment');
				}
				
				$pcc = new OrderPayment($this->_lastOrderPaymentId($order));
				$paymentIds[] = $pcc->id;
				
				// set card info
				$pcc->card_number = $trs->{'card_number'};
				$pcc->card_brand = $trs->{'card_brand'};
				if ($trs->{'expiry_month'} && $trs->{'expiry_year'}) {
					$pcc->card_expiration = str_pad($trs->{'expiry_month'}, 2, '0', STR_PAD_LEFT) . '/' . $trs->{'expiry_year'};
				}
				$pcc->card_holder = NULL;
				
				$pcc->update();
			}
		} else {
			$amount = $currency->convertAmountToFloat($payzenResponse->get('amount'));
			
			if (!$order->addOrderPayment($amount, null, $payzenResponse->get('trans_id'), null, null, $invoice)) {
				throw new PrestaShopException('Can\'t save Order Payment');
			}

			$pcc = new OrderPayment($this->_lastOrderPaymentId($order));
			$paymentIds[] = $pcc->id;
			
			// set card info
			$pcc->card_number = $payzenResponse->get('card_number');
			$pcc->card_brand = $payzenResponse->get('card_brand');
			if ($payzenResponse->get('expiry_month') && $payzenResponse->get('expiry_year')) {
				$pcc->card_expiration = str_pad($payzenResponse->get('expiry_month'), 2, '0', STR_PAD_LEFT) . '/' . $payzenResponse->get('expiry_year');
			}
			$pcc->card_holder = NULL;
	
			$pcc->update();
		}
		
		$paymentIds = implode(', ', $paymentIds);
		$this->logger->logInfo("Payment information with ID(s) {$paymentIds} saved successfully for cart #{$order->id_cart}.");
	}
	
	private function _isSuccessState($order) {
		return $order->getCurrentState() == Configuration::get('PS_OS_PAYMENT')
				|| $order->getCurrentState() == Configuration::get('PS_OS_OUTOFSTOCK')
				|| $order->getCurrentState() == Configuration::get('PAYZEN_OS_ONEY_PENDING')
				|| $order->getCurrentState() == Configuration::get('PAYZEN_OS_PAYMENT_OUTOFSTOCK');
	}
	
	private function _lastOrderPaymentId($order) {
		return Db::getInstance()->getValue('
				SELECT MAX(`id_order_payment`) FROM `'._DB_PREFIX_.'order_payment`
				WHERE `order_reference` = \'' . $order->reference . '\'');
	}
	
	public function isOney($payzenResponse) {
		return $payzenResponse->get('card_brand') == 'ONEY' || $payzenResponse->get('card_brand') == 'ONEY_SANDBOX';	
	}
	
	public function isOneyPendingPayment($payzenResponse) {
		return $this->isOney($payzenResponse) && $payzenResponse->isPendingPayment();
	}
}