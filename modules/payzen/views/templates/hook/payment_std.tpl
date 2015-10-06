{*
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
*}

<div class="payment_module payzen_payment_module">
	{if $payzen_std_card_data_mode == 1}
		<a onclick="javascript: $('#payzen_standard').submit();" title="{l s='Click here to pay by bank card' mod='payzen'}">
	{else} 
		<a class="unclickable" title="{l s='Enter payment information and click «Pay now» button' mod='payzen'}">
	{/if}
		<img class="logo" src="{$base_dir_ssl}modules/payzen/views/images/BannerLogo1.png" alt="PayZen"/>{$payzen_std_title}		
				
		<form action="{$link->getModuleLink('payzen', 'redirect', array(), true)}" method="post" id="payzen_standard" name="payzen_standard" class="payzen_payment_form">
			<input type="hidden" name="payzen_payment_type" value="standard" />
			
			{if ($payzen_std_card_data_mode == 2) OR ($payzen_std_card_data_mode == 3) }
				<br />
			
				{if {$payzen_avail_cards|@count} == 1}
					<input type="hidden" id="payzen_card_type_{$payzen_avail_cards.0}" name="payzen_card_type" value="{$payzen_avail_cards.0}" style="vertical-align: middle;">
					<label for="payzen_card_type_{$payzen_avail_cards.0}"><img style="vertical-align: middle; margin-right:10px; height:20px;" src="{$base_dir_ssl}modules/payzen/views/images/{$payzen_avail_cards.0|lower}.png" alt="{$payzen_avail_cards.0}" title="{$payzen_avail_cards.0}" /></label>
				{else}
					{assign var=first value=true}
					{foreach from=$payzen_avail_cards item="card"}
						{if $first == true}
							<input type="radio" id="payzen_card_type_{$card}" name="payzen_card_type" value="{$card}" style="vertical-align: middle;" checked="checked">
							{assign var=first value=false}
					  	{else}	
				    		<input type="radio" id="payzen_card_type_{$card}" name="payzen_card_type" value="{$card}" style="vertical-align: middle;">
				    	{/if}
						<label for="payzen_card_type_{$card}"><img style="vertical-align: middle; margin-right:10px; height:20px;" src="{$base_dir_ssl}modules/payzen/views/images/{$card|lower}.png" alt="{$card}" title="{$card}" /></label>
					{/foreach}
				{/if}
		
				{if $payzen_std_card_data_mode == 3}
					<br /><br />
					<label for="payzen_card_number"> {l s='Card number' mod='payzen'}</label><br />
					<input type="text" name="payzen_card_number" value="" autocomplete="off" maxlength="19" id="payzen_card_number" size="30" maxlength="16" />
					
					<br /><br />
					<label for="payzen_cvv"> {l s='CVV' mod='payzen'}</label><br />
					<input type="text" name="payzen_cvv" value="" autocomplete="off" maxlength="4" id="payzen_cvv" size="5" maxlength="4" />
					
					<br /><br />
					<label for="payzen_expiry_month">{l s='Expiration date' mod='payzen'}</label><br />
					<select name="payzen_expiry_month" id="payzen_expiry_month" style="width: 70px;">
						<option value="">{l s='Month' mod='payzen'}</option>
						{section name=expiry start=1 loop=13 step=1}
						<option value="{$smarty.section.expiry.index}">{$smarty.section.expiry.index}</option>
						{/section}
					</select>
								
					<select name="payzen_expiry_year" id="payzen_expiry_year" style="width: 70px;">
						<option value="">{l s='Year' mod='payzen'}</option>
						{assign var=year value=$smarty.now|date_format:"%Y"}
						{section name=expiry start=$year loop=$year+9 step=1}
				  		<option value="{$smarty.section.expiry.index}">{$smarty.section.expiry.index}</option>
						{/section}
					</select>
				{/if}
					
				<br /><br />
				{if $back_compat}
					<input type="submit" name="submit" value="{l s='Pay now' mod='payzen'}" class="button" />
				{else}
					<button type="submit" name="submit" class="button btn btn-default standard-checkout button-medium" >
						<span>{l s='Pay now' mod='payzen'}</span>
					</button>
				{/if}
			{/if}
		</form>
	</a>
	
	{if $payzen_std_card_data_mode == 3} {literal}
		<script type="text/javascript">
			$(document).ready(function() {
				$('#payzen_standard').bind('submit', function() {
					$('#payzen_standard input, #payzen_standard select').each(function() {
						$(this).removeClass('invalid');
					});
					
					var cardNumber = $('#payzen_card_number').val();
					if(cardNumber.length <= 0 || !(/^\d{13,19}$/.test(cardNumber))){
						$('#payzen_card_number').addClass('invalid');
					}
						
					var cvv = $('#payzen_cvv').val();
					if(cvv.length <= 0 || !(/^\d{3,4}$/.test(cvv))) {
						$('#payzen_cvv').addClass('invalid');
					}	
						
					var currentTime  = new Date();
					var currentMonth = currentTime.getMonth() + 1;
					var currentYear  = currentTime.getFullYear();
					
					var expiryYear = $('select[name="payzen_expiry_year"] option:selected').val();
					if(expiryYear.length <= 0 || !(/^\d{4}$/.test(expiryYear)) || expiryYear < currentYear) {
						$('#payzen_expiry_year').addClass('invalid');
					}
						
					var expiryMonth = $('select[name="payzen_expiry_month"] option:selected').val();
					if(expiryMonth.length <= 0 || !(/^\d{1,2}$/.test(expiryMonth)) || (expiryYear == currentYear && expiryMonth < currentMonth)) {
						$('#payzen_expiry_month').addClass('invalid');
					}
						
					return ($('#payzen_standard').find('.invalid').length > 0) ? false : true;
				});
			});
		</script>
	{/literal} {/if}
</div>