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
 * Class that renders PayZen payment module administration interface.
 */
class PayzenAdminDisplay {
	private $_module = null;
	private $_name = null;
	private $_displayName = null;
	
	public function __construct($payzen) {
		$this->_module = $payzen;
		
		$this->_name = $this->_module->name;
		$this->_displayName = $this->_module->displayName;
	}
	
	/**
	 * Builds the html code for the admin form
	 */
	public function commonHtml() {
	
		// Form beginning
		$_html = '<fieldset><legend><img style="width: 16px;" src="../modules/' . $this->_name . '/logo.png" alt="PayZen"/>' . $this->_displayName . '</legend>'
				. $this->l('Developped by')
				. ' : <b><a href="http://www.lyra-network.com/" target="_blank">Lyra Network</a></b><br/>'
				. $this->l('Contact email')
				. ' : <b><a href="mailto:support@payzen.eu">support@payzen.eu</a></b><br/>'
				. $this->l('Module version')
				. ' : <b>1.2f</b><br/>'
				. $this->l('Compatible with payment gateway')
				. ' : <b>V2</b><br/>'
				. $this->l('Tested with prestashop version').
				' : <b>1.5 & 1.6</b><br />'
				. '<a style="color: red;" href="../modules/payzen/installation_doc/Integration_PayZen_Prestashop_1.5.0.x_v1.2f.pdf" target="_blank"><b>'
				. $this->l('CLICK HERE TO VIEW THE MODULE CONFIGURATION DOCUMENTATION') . '</b></a>' .
			'</fieldset>';
	
		return $_html;
	}
	
	public function generalTabHtml() {
		$api = new PayzenApi(); // new instance of PayzenApi to use static methods
		
		$_html = '<fieldset><legend>' . $this->l('BASE SETTINGS') . '</legend>';
		// enable / disbale logs
		$options = array(
				'False' => $this->l('Disabled'),
				'True' => $this->l('Enabled')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_ENABLE_LOGS'), 'PAYZEN_ENABLE_LOGS', $this->l('Logs'), $this->l('Enable / disbale module logs'));
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * payment gateway access settings
		*/
		$_html .= '<fieldset><legend>' . $this->l('PAYMENT GATEWAY ACCESS') . '</legend>';
		
		$_html .= $this->_adminFormText('PAYZEN_SITE_ID', $this->l('Site id'), $this->l('Site id provided by the payment gateway'));
		$_html .= $this->_adminFormText('PAYZEN_KEY_TEST', $this->l('Test certificate'), $this->l('Certificate provided by the gateway'));
		$_html .= $this->_adminFormText('PAYZEN_KEY_PROD', $this->l('Production certificate'), $this->l('Certificate provided by the gateway'));
		
		// context mode
		$options = array(
				'TEST' => $this->l('TEST'),
				'PRODUCTION' => $this->l('PRODUCTION')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_MODE'), 'PAYZEN_MODE', $this->l('Mode'), $this->l('The context mode of this module'));
		$_html .= $this->_adminFormText('PAYZEN_PLATFORM_URL', $this->l('Gateway URL'), $this->l('URL the client will be redirected to'), 'size="65"');
		
		// check URL display
		$_html .= '<label>' . $this->l('Check URL to copy in your bank back-office') . '</label>';
		$_html .= '<div class="margin-form"><p>';
		if(!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP) {
			$_html .= Context::getContext()->shop->getBaseURL() . 'modules/' . $this->_name . '/validation.php';
		} else {
			$_html .= $this->l('Select a shop to view check URL');
		}
		$_html .= '</p></div>';
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
	
		/*
		 * payment page settings
		 */
		$_html .= '<fieldset><legend>' . $this->l('PAYMENT PAGE') . '</legend>';
		
		// supported languages
		$options = array();
		foreach ($api->getSupportedLanguages() as $key => $value) {
			$options[$key] = $this->l($value);
		}
		asort($options);
		
		// default language
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_DEFAULT_LANGUAGE'), 'PAYZEN_DEFAULT_LANGUAGE', $this->l('Default language'), $this->l('Default language on the payment page'));
		
		// available languages
		$availLangs = Configuration::get('PAYZEN_AVAILABLE_LANGUAGES');
		$selected = ($availLangs == '') ? array() : explode(';', $availLangs);
		
		$_html .= $this->_adminFormSelect($options, $selected, 'PAYZEN_AVAILABLE_LANGUAGES[]', $this->l('Available languages'), $this->l('Select none to use gateway configuration.'), 'multiple="multiple" size="8"');
	
		// capture delay
		$_html .= $this->_adminFormText('PAYZEN_DELAY', $this->l('Delay'), $this->l('Delay before banking (in days)'));
		
		// validation mode
		$options = array(
				'' => $this->l('Back-office configuration'),
				'0' => $this->l('Automatic'),
				'1' => $this->l('Manual')
		);
		
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_VALIDATION_MODE'), 'PAYZEN_VALIDATION_MODE', $this->l('Payment validation'), $this->l('If manual is selected, you will have to confirm payments manually in your bank back-office'));
		
		// payment cards
		$paymentCards = Configuration::get('PAYZEN_PAYMENT_CARDS');
		$selected = ($paymentCards == '') ? array() : explode(';', $paymentCards);
		
		$_html .= $this->_adminFormSelect($api->getSupportedCardTypes(), $selected, 'PAYZEN_PAYMENT_CARDS[]', $this->l('Available payment cards'), $this->l('Select the card types that can be used for the payment.'), 'multiple="multiple" size="7"');
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * payment page customization settings
		*/
		$_html .= '<fieldset><legend>' . $this->l('PAYMENT PAGE CUSTOMIZE') . '</legend>';
		
		// theme configuration
		$_html .= $this->_adminFormText('PAYZEN_THEME_CONFIG', $this->l('Theme configuration'), $this->l('The theme configuration to customize the payment page (logo, css).'), 'size="65"');
		
		// shop name
		$_html .= $this->_adminFormText('PAYZEN_SHOP_NAME', $this->l('Shop name'), $this->l('Shop name to display on the payment page. Leave blank to use gateway config.'));
		
		// shop URL
		$_html .= $this->_adminFormText('PAYZEN_SHOP_URL', $this->l('Shop URL'), $this->l('Shop URL to display on the payment page. Leave blank to use gateway config.'), 'size="65"');
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * selective 3DS settings
		*/
		$_html .= '<fieldset><legend>' . $this->l('SELECTIVE 3DS') . '</legend>';
		
		// min amount to activate three ds
		$_html .= $this->_adminFormText('PAYZEN_3DS_MIN_AMOUNT', $this->l('Minimum amount for which activate 3DS'), $this->l('Requires subscription to Selective 3-D Secure option.'));

		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
	
		/*
		 * return to shop settings
		 */
		$_html .= '<fieldset><legend>' . $this->l('RETURN TO SHOP') . '</legend>';
		
		// automatic redirection
		$options = array(
				'False' => $this->l('Disabled'),
				'True' => $this->l('Enabled')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_REDIRECT_ENABLED'), 'PAYZEN_REDIRECT_ENABLED', $this->l('Automatic redirection'), $this->l('Redirect the client to the shop at the end of the payment process'));
		
		// automatic redirection options
		$_html .= $this->_adminFormText('PAYZEN_REDIRECT_SUCCESS_T', $this->l('Success timeout'), $this->l('Time before the client is redirected after a successful payment'));
		$_html .= $this->_adminFormText('PAYZEN_REDIRECT_SUCCESS_M', $this->l('Success message'), $this->l('Message displayed before redirection after a successful payment'), 'size="65"');
		$_html .= $this->_adminFormText('PAYZEN_REDIRECT_ERROR_T', $this->l('Failure timeout'), $this->l('Time before the client is redirected after a failed payment')); 
		$_html .= $this->_adminFormText('PAYZEN_REDIRECT_ERROR_M', $this->l('Failure message'), $this->l('Message displayed before redirection after a failed payment'), 'size="65"');
	
		// return mode
		$options = array(
				'GET' => $this->l('GET (parameters in URL)'),
				'POST' => $this->l('POST (parameters in a form)')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_RETURN_MODE'), 'PAYZEN_RETURN_MODE', $this->l('Return mode'), $this->l('How the client will transmit the payment result'));
	
		// payment failed management
		$options = array(
				Payzen::ON_FAILURE_RETRY => $this->l('Go back to checkout'),
				Payzen::ON_FAILURE_SAVE => $this->l('Save order and go back to order history')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_FAILURE_MANAGEMENT'), 'PAYZEN_FAILURE_MANAGEMENT', $this->l('Payment failed management'), $this->l('How to deal the client when the payment process failed'));
			
		// additional return parameters
		$_html .= $this->_adminFormText('PAYZEN_RETURN_GET_PARAMS', $this->l('Additional GET parameters'), $this->l('Extra parameters sent in the return URL'), 'size="65"');
	
		$_html .= $this->_adminFormText('PAYZEN_RETURN_POST_PARAMS', $this->l('Additional POST parameters'), $this->l('Extra parameters sent in the return form'), 'size="65"');
	
		$_html .= '</fieldset>';
		
		return $_html;
	}
	
	public function singleTabHtml() {
		/*
		 * standard payment mode specific settings
		*/
		$_html = '<fieldset><legend>' . $this->l('MODULE OPTIONS') . '</legend>';
	
		// title
		$_html .= $this->_adminFormTextLang('PAYZEN_STD_TITLE', $this->l('Method title'), $this->l('Method title to display on payment means page.'), 'size="45"');
		
		// enable/disable module
		$options = array(
				'False' => $this->l('Disabled'),
				'True' => $this->l('Enabled')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_STD_ENABLED'), 'PAYZEN_STD_ENABLED', $this->l('Activation'), $this->l('Select to activate standard payment'));
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * disable payment mode for specific amounts
		 */
		$_html .= '<fieldset><legend>' . $this->l('AMOUNT RESTRICTIONS') . '</legend>';
		
		$_html .= $this->_adminFormText('PAYZEN_STD_AMOUNT_MIN', $this->l('Minimum amount'), $this->l('Minimum amount for which this payment method is available'));
		$_html .= $this->_adminFormText('PAYZEN_STD_AMOUNT_MAX', $this->l('Maximum amount'), $this->l('Maximum amount for which this payment method is available'));
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * card data entry mode
		*/
		$_html .= '<fieldset><legend>' . $this->l('CARD DATA ENTRY') . '</legend>';
	
		$options = array(
				'1' => $this->l('Card data entry on payment gateway'),
				'2' => $this->l('Card type selection on merchant site'),
				'3' => $this->l('Card data entry on merchant site')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_STD_CARD_DATA_MODE'), 'PAYZEN_STD_CARD_DATA_MODE', $this->l('Card data entry mode'), $this->l('Select how the credit card info will be entered.<br /> Attention, to use data acquisition on the merchant site, you must ensure that you have subscribed to this option with your bank.'));
		
		$_html .= '</fieldset>';
	
		return $_html;
	}
	
	public function multiTabHtml() {
		/*
		 * multiple payment mode specific settings
		*/
		$_html = '<fieldset><legend>' . $this->l('MODULE OPTIONS') . '</legend>';
	    
		// title
		$_html .= $this->_adminFormTextLang('PAYZEN_MULTI_TITLE', $this->l('Method title'), $this->l('Method title to display on payment means page.'), 'size="45"');
		
		// enable/disable module
		$options = array(
				'False' => $this->l('Disabled'),
				'True' => $this->l('Enabled')
		);
		$_html .= $this->_adminFormSelect($options, Configuration::get('PAYZEN_MULTI_ENABLED'), 'PAYZEN_MULTI_ENABLED', $this->l('Activation'), $this->l('Select to activate multiple payment'));
	
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * disable payment mode for specific amounts
		 */
		$_html .= '<fieldset><legend>' . $this->l('AMOUNT RESTRICTIONS') . '</legend>';
		
		$_html .= $this->_adminFormText('PAYZEN_MULTI_AMOUNT_MIN', $this->l('Minimum amount'), $this->l('Minimum amount for which this payment method is available'));
		$_html .= $this->_adminFormText('PAYZEN_MULTI_AMOUNT_MAX', $this->l('Maximum amount'), $this->l('Maximum amount for which this payment method is available'));
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * multiple payment options
		 */
		$multiOptions = Configuration::get('PAYZEN_MULTI_OPTIONS');
		if(!empty($multiOptions)) {
			$multiOptions = unserialize($multiOptions);
		} else {
			$multiOptions = array();
		}
		
		$_html .= '<fieldset><legend>' . $this->l('MULTIPLE PAYMENT OPTIONS') . '</legend>';
		
		$_html .= "\n";
		$_html .= '<label for="PAYZEN_MULTI_OPTIONS">' . $this->l('Payment options') . '</label>';
		$_html .= '<div class="margin-form">';
		$_html .= '<input id="payzen_multi_options_btn"' . (!empty($multiOptions) ? ' style="display: none;"' : '') . ' type="button" value="' . $this->l('Add') . '" onclick="javascript: payzenAddOption(true, \'' . $this->l('Delete') . '\');" />';
		$_html .= '<table id="payzen_multi_options_table"' . (empty($multiOptions) ? ' style="display: none;"' : '') . ' cellpadding="10" cellspacing="0" class="table">
		           <thead>
						<tr>
							<th>'.$this->l('Label').'</th>
							<th>'.$this->l('Min amount').'</th>
							<th>'.$this->l('Max amount').'</th>
							<th>'.$this->l('Contract').'</th>
							<th>'.$this->l('Number').'</th>
							<th>'.$this->l('Period').'</th>
							<th>'.$this->l('1st payment').'</th>
							<th></th>
						</tr>
					</thead>
					
					<tbody>';
						if(!empty($multiOptions)) {
							foreach ($multiOptions as $key => $option) {
								$_html .= '<tr id="payzen_multi_option_' . $key . '">
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][label]" style="width: 150px;" type="text" value="' . $option['label']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][amount_min]" style="width: 80px;" type="text" value="' . $option['amount_min']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][amount_max]" style="width: 80px;" type="text" value="' . $option['amount_max']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][contract]" style="width: 70px;" type="text" value="' . $option['contract']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][count]" style="width: 70px;" type="text" value="' . $option['count']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][period]" style="width: 70px;" type="text" value="' . $option['period']. '"/></td>
									<td><input name="PAYZEN_MULTI_OPTIONS[' .$key. '][first]" style="width: 70px;" type="text" value="' . $option['first']. '"/></td>
									<td><input style="width: 70px;" type="button" value="' . $this->l('Delete') . '" onclick="javascript: payzenDeleteOption(' . $key . ');"/></td>
									</tr>';
							}
						}
					
					$_html .= '
						<tr id="payzen_multi_option_add">
							<td colspan="7"></td>
							<td><input type="button" value="'.$this->l('Add').'" onclick="javascript: payzenAddOption(false, \'' . $this->l('Delete') . '\');" /></td>
						</tr>
					</tbody>
		       </table>';
		
		$_html .= '<p>' . $this->l('Click on Add button to configure one or more payment options.') . '<br />';
		$_html .= '<b>' . $this->l('Label') . ' : </b>' . $this->l('The option label to display on the frontend.') . '<br />';
		$_html .= '<b>' . $this->l('Min amount') . ' : </b>' . $this->l('Minimum amount to enable the payment option.') . '<br />';
		$_html .= '<b>' . $this->l('Max amount') . ' : </b>' . $this->l('Maximum amount to enable the payment option.') . '<br />';
		$_html .= '<b>' . $this->l('Contract') . ' : </b>' . $this->l('ID of the contract to use with the option (Leave blank preferably).') . '<br />';
		$_html .= '<b>' . $this->l('Number') . ' : </b>' . $this->l('Total number of payments.') . '<br />';
		$_html .= '<b>' . $this->l('Period') . ' : </b>' . $this->l('Delay (in days) between payments.') . '<br />';
		$_html .= '<b>' . $this->l('1st payment') . ' : </b>' . $this->l('Amount of first payment, in percentage of total amount. If empty, all payments will have the same amount.') . '<br />';
		$_html .= '<b>' . $this->l('Do no forget to clik on "Save" button to save your modifications.') . '</b></p>';
		$_html .= '</div>';
		
		$_html .= '</fieldset>';
		
		return $_html;
	}
	
	public function oneyTabHtml() {
		$_html = '<fieldset><legend>' . $this->l('MODULE OPTIONS') . '</legend>';
	
		$_html .= $this->_adminFormTextLang('PAYZEN_ONEY_TITLE', $this->l('Method title'),
				$this->l('Method title to display on payment means page.'), 'size="45"');
	
		//activate Oney payment mode
		$options = array(
				'False' => $this->l('Disabled'),
				'True' => $this->l('Enabled')
		);
	
		$selected = key_exists(Configuration::get('PAYZEN_ONEY_ENABLED'), $options) ? Configuration::get('PAYZEN_ONEY_ENABLED') : 'False';
		$_html .= $this->_adminFormSelect($options,$selected,'PAYZEN_ONEY_ENABLED',
				$this->l('Activation'),
				$this->l('Select to activate FacilyPay Oney payment') . '<br />' . 
				'<b>' . sprintf($this->l('Requires a FacilyPay Oney contract with Banque Accord and a subscription to %s option'), 'PayZen') . '</b>');
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
	    
		/*
		 * Deactivate for specific amounts
		*/
		$_html .= '<fieldset><legend>' . $this->l('AMOUNT RESTRICTIONS') . '</legend>';
		$_html .= $this->_adminFormText('PAYZEN_ONEY_AMOUNT_MIN', $this->l('Minimum amount'),
				$this->l('Minimum amount for which this payment method is available'));
		$_html .= $this->_adminFormText('PAYZEN_ONEY_AMOUNT_MAX', $this->l('Maximum amount'),
				$this->l('Maximum amount for which this payment method is available'));
		
		$_html .= '</fieldset><div class="clear">&nbsp;</div>';
		
		/*
		 * Oney shipping settings
		*/
		$_html .= '<fieldset><legend>' . $this->l('FACILYPAY ONEY SHIPPING SETTINGS') . '</legend>';
	
		$oneyOptions = @unserialize(Configuration::get('PAYZEN_ONEY_SHIP_OPTIONS'));
		if(!is_array($oneyOptions)) {
			$oneyOptions = array();
		}
		$carriers = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, false, null, Carrier::ALL_CARRIERS);
	
		$_html .= '<label for="PAYZEN_ONEY_SHIP_OPTIONS">' . $this->l('Shipping options') . '</label>';
		$_html .= '<div class="margin-form">';
	
		$typeOptions = array(
				'PACKAGE_DELIVERY_COMPANY' => $this->l('Delivery company'),
				'RECLAIM_IN_SHOP' => $this->l('Reclaim in shop'),
				'RELAY_POINT' => $this->l('Relay point'),
				'RECLAIM_IN_STATION' => $this->l('Reclaim in station')
		);
		
		$speedOptions = array(
				'STANDARD' => $this->l('Standard'),
				'EXPRESS' => $this->l('Express')
		);
		
		$_html .= '<table id="payzen_oney_options_table" cellpadding="10" cellspacing="0" class="table">
				   <thead><tr>
						<th>'.$this->l('Name').'</th>
						<th>'.$this->l('Label').'</th>
						<th>'.$this->l('Type').'</th>
						<th>'.$this->l('Speed').'</th>
						<th style="width: 300px;">'.$this->l('Address').'</th>
				    </tr></thead>';
			
		$_html .= '<tbody>';
	
		if(!empty($carriers)) {
			foreach ($carriers as $carrier) {
				$carrierId = $carrier['id_carrier'];
	
				$selected = key_exists(@$oneyOptions[$carrierId]['delivery_speed'], $speedOptions) ? $oneyOptions[$carrierId]['delivery_speed'] : 'STANDARD';
				$selectSpeed = '<select name="PAYZEN_ONEY_SHIP_OPTIONS[' .$carrierId. '][delivery_speed]" >';
				foreach ($speedOptions as $value => $label) {
					$selectSpeed .= '<option value="' . $value . '"';
					$selectSpeed .= ((string) $value == (string) $selected) ? ' selected="selected"' : '';
					$selectSpeed .= '>' . $label . '</option>';
				}
				$selectSpeed .= '</select>';
				
				$selected = key_exists(@$oneyOptions[$carrierId]['delivery_type'], $typeOptions) ? $oneyOptions[$carrierId]['delivery_type'] : 'PACKAGE_DELIVERY_COMPANY';
				$selectType = '<select onchange="javascript: payzenTypeChanged(' . $carrierId . ');"  id="payzen_oney_type_' . $carrierId . '" name="PAYZEN_ONEY_SHIP_OPTIONS[' . $carrierId . '][delivery_type]" style="width: 150px;" >';
				foreach ($typeOptions as $value => $label) {
					$selectType .= '<option value="' . $value . '"';
					$selectType .= ((string) $value == (string) $selected) ? ' selected="selected"' : '';
					$selectType .= '>' . $label . '</option>';
				}
				$selectType .= '</select>';
				
				$carrierNameRegexNotAllowed = "#[^A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ /'-]#ui";
				$carrierLabel = isset($oneyOptions[$carrierId]['carrier_label']) ?
									 $oneyOptions[$carrierId]['carrier_label']:
				              		 preg_replace($carrierNameRegexNotAllowed, ' ' , $carrier['name']);
			      
				$_html .= '<tr id="payzen_oney_options_' . $carrierId . '">
							<td>' . $carrier['name'] . '<input type="hidden" name="PAYZEN_ONEY_SHIP_OPTIONS[' . $carrierId . '][carrier_name]" value="' . $carrier['name'] . '"/></td>
							<td><input type="text" name="PAYZEN_ONEY_SHIP_OPTIONS[' . $carrierId . '][carrier_label]" value="' . $carrierLabel . '"/></td>
							<td>' . $selectType . '</td>
							<td>' . $selectSpeed . '</td>
							<td><input id="payzen_oney_address_' . $carrierId . '" name="PAYZEN_ONEY_SHIP_OPTIONS[' . $carrierId . '][address]" style="width: 300px;' . ((string)$selected != 'RECLAIM_IN_SHOP' ? ' display: none;' : '') . '" type="text" value="' . @$oneyOptions[$carrierId]['address'] . '" /></td>
						</tr>';
			}
		}
		
		$_html .='</tbody></table>';
		
		$_html .= '<p>' . $this->l('Define the FacilyPay Oney information about all shipping methods.') . '<br />';
		$_html .= '<b>' . $this->l('Label') . ' : </b>' . $this->l('The label of the shipping method.') . '<br />';
		$_html .= '<b>' . $this->l('Type') . ' : </b>' . $this->l('The delivery type of shipping method.') . '<br />';
		$_html .= '<b>' . $this->l('Speed') . ' : </b>' . $this->l('Select whether the delivery is STANDARD or EXPRESS.') . '<br />';
		$_html .= '<b>' . $this->l('Address') . ' : </b>' . $this->l('Enter address if it is a reclaim in shop.') . '<br />';
		$_html .= '<b>' . $this->l('Do no forget to clik on "Save" button to save your modifications.') . '</b></p>';
		$_html .= '</div>';
	
		$categoryOptions = array(
				'FOOD_AND_GROCERY' => $this->l('Food and grocery'),
				'AUTOMOTIVE' => $this->l('Automotive'),
				'ENTERTAINMENT' => $this->l('Entertainment'),
				'HOME_AND_GARDEN' => $this->l('Home and garden'),
				'HOME_APPLIANCE' => $this->l('Home appliance'),
				'AUCTION_AND_GROUP_BUYING' => $this->l('Auction and group buying'),
				'FLOWERS_AND_GIFTS' => $this->l('Flowers and gifts'),
				'COMPUTER_AND_SOFTWARE' => $this->l('Computer and software'),
				'HEALTH_AND_BEAUTY' => $this->l('Health and beauty'),
				'SERVICE_FOR_INDIVIDUAL' => $this->l('Service for individual'),
				'SERVICE_FOR_BUSINESS' => $this->l('Service for business'),
				'SPORTS' => $this->l('Sports'),
				'CLOTHING_AND_ACCESSORIES' => $this->l('Clothing and accessories'),
				'TRAVEL' => $this->l('Travel'),
				'HOME_AUDIO_PHOTO_VIDEO' => $this->l('Home audio,photo,video'),
				'TELEPHONY' => $this->l('Telephony')
		);
		
		$_html .= '<label for="PAYZEN_ONEY_PROD_CAT">' . $this->l('Product categories options') . '</label>';
		$_html .= '<div class="margin-form">';
		
		$checked = '';
		$disableTable = "";
		$disabled ='disabled="disabled"';
		$value = '0';
		if(@Configuration::get('PAYZEN_ONEY_CAT_MODE') == '1' ) {
			    $value = '1' ;
				$checked ='checked="checked"';
				$disabled ='';
				$disableTable ='disabled="disabled"';
		}
	    $_html .='<input type="checkbox" id="payzen_oney_cat_mode" onclick="disableTable(this)"'.$checked.' name="PAYZEN_ONEY_CAT_MODE" value="'.$value.'">&nbsp;<label for="payzen_oney_cat_mode" style="float:none;">' . $this->l('Use the same category for all products') . '</label>&nbsp;&nbsp;';
	 
	    $selected = key_exists(@Configuration::get('PAYZEN_ONEY_COM_CAT'),
											 $categoryOptions) ? Configuration::get('PAYZEN_ONEY_COM_CAT') : 'FOOD_AND_GROCERY';
		$selectCategory = '<select '.$disabled.' name="PAYZEN_ONEY_COM_CAT" id="payzen_oney_com_cat" style="width: 180px;" >';
		foreach ($categoryOptions as $value => $label) {
				$selectCategory .= '<option value="' . $value . '"';
				$selectCategory .= ((string) $value == (string) $selected) ? ' selected="selected"' : '';
			    $selectCategory .= '>' . $label . '</option>';
		}
		$selectCategory .= '</select> ';
							
		$_html .= $selectCategory . '<br /><br />';
		
		$_html .= 	'<table id="payzen_oney_categories_table" cellpadding="10" cellspacing="0" class="table">
				  	<thead><tr>
						<th>'.$this->l('Product category').'</th>
						<th>'.$this->l('FacilyPay Oney product category').'</th>
					</tr></thead>';
			
		$_html .= '<tbody>';
		
		$oneyCategories = @unserialize(Configuration::get('PAYZEN_ONEY_PROD_CAT'));
		if(!is_array($oneyCategories)) {
			$oneyCategories = array();
		}
		
		$categoriesArray = Category::getCategories((int)Configuration::get('PS_LANG_DEFAULT'), true, true, '', '', '');
		
		if(!empty($categoriesArray)) {
			foreach ($categoriesArray as $categoryArray) {
				foreach ($categoryArray as $categories) {
					foreach ($categories as $category) {
						if ($category['name'] != 'Root') {
							$categoryId = $category['id_category'];
							$selected = key_exists(@$oneyCategories[$categoryId], $categoryOptions) ? 
										$oneyCategories[$categoryId] : 'FOOD_AND_GROCERY';
							$selectCategory = '<select '.$disableTable.' name="PAYZEN_ONEY_PROD_CAT[' . $categoryId . ']" style="width: 180px;" >';
							foreach ($categoryOptions as $value => $label) {
								$selectCategory .= '<option value="' . $value . '"';
								$selectCategory .= ((string) $value == (string) $selected) ? ' selected="selected"' : '';
								$selectCategory .= '>' . $label . '</option>';
							}
							$selectCategory .= '</select>';
									
							$_html .= 	'<tr id="payzen_oney_categories_' . $categoryId . '">
											<td>' . $category['name'] . '</td>
											<td>' . $selectCategory . '</td>
										</tr>';
						}	
					}
				}
			}
		}
		$_html .='</tbody></table>';
		
		$_html .= '<p>' . $this->l('Select one category for all products or match each product category with an FacilyPay Oney category.').'</p>';
		$_html .= '</div>';
		
		$_html .= '</fieldset>';
		return $_html;
	}
	  
	/**
	 * Shortcut function for creating a html select
	 * @param array[string]string $options
	 * @param mixed $selected a single string value or an array
	 * @param string $name
	 * @param string $label
	 * @param string $description
	 * @param string $extra_attributes
	 */
	private function _adminFormText($name, $label, $description = null, $extra_attributes = '', $default='') {
		$value = Configuration::get($name) ? Configuration::get($name) : $default;
	  	$_html = "\n";
	  	$_html .= '<label for="' . $name . '">' . $label . '</label>';
	  	$_html .= '<div class="margin-form">';
	  	$_html .= '<input type="text" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $extra_attributes . '/>';
	  	$_html .= '<p>' . $description . '</p>';
	  	$_html .= '</div>';
	  	return $_html;
	}
	  
	private function _adminFormSelect($options, $selected, $name, $label, $description, $extra_attributes = null) {
	  	$_html = "\n";
	  	$_html .= '<label for="' . $name . '">' . $label . '</label>';
	  	$_html .= '<div class="margin-form">';
	  	$_html .= '<select name="' . $name . '" ' . $extra_attributes . '>';
	  	foreach ($options as $value => $label) {
	  		$_html .= '<option value="' . $value . '"';
			$is_selected = is_array($selected) ? in_array($value, $selected) : ((string) $value == (string) $selected);
	  		$_html .= $is_selected ? ' selected="selected"' : '';
	  		$_html .= '>' . $label . '</option>';
	  	}
	  	$_html .= '</select><p>' . $description . '</p></div>';
	  	return $_html;
	}
	
	private function _adminFormTextLang($name, $label, $description = null, $extra_attributes = '', $default=array()) {
		$_html = "\n";
		$_html .= '<label for="'.$name.'">' . $label . '</label>';
		$_html .= '<div class="margin-form">';

		$languages = Language::getLanguages(false);
		foreach ($languages as $language) {
			$langId = (int)$language['id_lang'];
			$langCode = $language['iso_code'];
			
			$value = Configuration::get($name, $langId) ? 
					 Configuration::get($name, $langId) :
					 (is_array($default) && isset($default[$langCode]) ? $default[$langCode] : '');
			
			$_html .= '<div id="'.$name.'_'.$langId.'" style="float: left; display: '.($langId == Context::getContext()->language->id ? 'block' : 'none').';">';
			$_html .= '<input type="text" id="'.$name.'_'.$langId.'" name="'.$name.'_'.$langId.'" value="'.$value.'" ' . $extra_attributes . ' />';
			$_html .= '</div>';
		}
		$_html .= $this->_module->displayFlags($languages, Context::getContext()->language->id, $name, $name, true);
		
		$_html .= '<br class="clear" /><p>' . $description . '</p>';
		$_html .= '</div>';

		return $_html;
	}
	
	/**
	 * Shortcut for module translation function.
	 * 
	 * @param string $text
	 * 
	 * @return localized text
	 */
	private function l($string) {
		return $this->_module->l($string, 'payzenadmindisplay', null);
	}
}