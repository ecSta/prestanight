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

/**
 * Misc JavaScript functions.
 */

function payzenAddOption(first, deleteText) {
	if(first) {
		$('#payzen_multi_options_btn').css('display', 'none');
		$('#payzen_multi_options_table').css('display', '');
	}
	
	var timestamp = new Date().getTime();
	
	var optionLine = '<tr id="payzen_multi_option_' + timestamp + '">' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][label]" style="width: 150px;" type="text"/></td>' + 
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][amount_min]" style="width: 80px;" type="text"/></td>' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][amount_max]" style="width: 80px;" type="text"/></td>' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][contract]" style="width: 70px;" type="text"/></td>' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][count]" style="width: 70px;" type="text"/></td>' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][period]" style="width: 70px;" type="text"/></td>' +
					 '	<td><input name="PAYZEN_MULTI_OPTIONS[' + timestamp + '][first]" style="width: 70px;" type="text"/></td>' +
					 '	<td><input type="button" value="' + deleteText + '" onclick="javascript: payzenDeleteOption(' + timestamp + ');"/></td>' +
					 '</tr>';
							
	$(optionLine).insertBefore('#payzen_multi_option_add');
}

function payzenDeleteOption(key) {
	$('#payzen_multi_option_' + key).remove();
	
	if($('#payzen_multi_options_table tbody tr').length == 1) {
		$('#payzen_multi_options_btn').css('display', '');
		$('#payzen_multi_options_table').css('display', 'none');
	}	
}

function payzenTypeChanged(key) {
	var type = $('#payzen_oney_type_' + key).val();
	
	if(type == 'RECLAIM_IN_SHOP') {
		$('#payzen_oney_address_' + key).css('display', '');
	} else {
		$('#payzen_oney_address_' + key).val('');
		$('#payzen_oney_address_' + key).css('display', 'none');
	}
}

function disableTable(e) {
    $('#payzen_oney_categories_table select').attr('disabled', e.checked);
    document.getElementById('payzen_oney_com_cat').disabled = !e.checked; 
    if (e.checked) {
	    e.value = '1';
	} else {
		e.value = '0';
	}
 }

// init selected tab in backoffice gui
var pos_select = 0;

function loadTab(id) {
	// static tabs, do nothing
}