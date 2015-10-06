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
	<a class="unclickable" title="{l s='Select payment option and click «Pay now» button' mod='payzen'}">
		<img class="logo" src="{$base_dir_ssl}modules/payzen/views/images/BannerLogo2.png" alt="PayZen"/>{$payzen_multi_title}
		
		<form action="{$link->getModuleLink('payzen', 'redirect', array(), true)}" method="post" name="payzen_multi" class="payzen_payment_form" >
			<input type="hidden" name="payzen_payment_type" value="multi" />
			<br />
			
			{if {$payzen_multi_options|@count} == 1}
				{foreach from=$payzen_multi_options key="key" item="option"}
			   		<input type="hidden" id="payzen_opt_{$key}" name="payzen_opt" value="{$key}" style="vertical-align: middle;" />
			      	<label for="payzen_opt_{$key}">{$option.label}</label>
			      	<br />
		 		{/foreach}	 
			{else}
				{assign var=first value=true}
				{foreach from=$payzen_multi_options key="key" item="option"}
					{if $first == true}
						{assign var=checked value='checked="checked"'}
						{assign var=first value=false}
				    {else}
				    	{assign var=checked value=''}
				    {/if}
				    
					<input type="radio" id="payzen_opt_{$key}" name="payzen_opt" value="{$key}" style="vertical-align: middle;" {$checked} />
				    <label for="payzen_opt_{$key}">{$option.label}</label>
				    <br />
		   		{/foreach}
			{/if}
		 		
			<br />
			{if $back_compat}
				<input type="submit" name="submit" value="{l s='Pay now' mod='payzen'}" class="button" />
			{else}
				<button type="submit" name="submit" class="button btn btn-default standard-checkout button-medium" >
					<span>{l s='Pay now' mod='payzen'}</span>
				</button>
			{/if}
		</form>
	</a>
</div>