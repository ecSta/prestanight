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

{capture name=path}PayZen{/capture}
{if $back_compat}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{if isset($payzen_params) && $payzen_params.vads_action_mode == 'SILENT'}
	<h2>{l s='Payment processing' mod='payzen'}</h2>
{else}
	<h2>{l s='Redirection to payment gateway' mod='payzen'}</h2>
{/if}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($payzen_empty_cart) && $payzen_empty_cart}
	<p class="warning">{l s='Your shopping cart is empty.' mod='payzen'}</p>
{else}
	<h3>{l s='Payment by bank card' mod='payzen'}</h3>
	
	<form action="{$payzen_url}" method="post" id="payzen_form" name="payzen_form"> 
	    {foreach from=$payzen_params key='key' item='value'}
			<input type="hidden" name="{$key}" value="{$value}" />
		{/foreach}

		<p>
			<img src="{$base_dir}modules/payzen/views/images/{$payzen_logo}" alt="PayZen" style="margin-bottom: 5px" />
			<br />
			
			{if $payzen_params.vads_action_mode == 'SILENT'}
				{l s='Please wait a moment. Your order payment is now processing.' mod='payzen'}
			{else}
				{l s='Please wait, you will be redirected to the payment platform.' mod='payzen'}
			{/if}
			
			<br /> <br />
			{l s='If nothing happens in 10 seconds, please click the button below.' mod='payzen'} 
			<br /><br />
		</p>
	
	{if $back_compat}
		<p class="cart_navigation">
			<input type="submit" name="submitPayment" value="{l s='Pay' mod='payzen'}" class="exclusive" />
		</p>
	{else}
		<p class="cart_navigation clearfix">
			<button type="submit" name="submitPayment" class="button btn btn-default standard-checkout button-medium" >
				<span>{l s='Pay' mod='payzen'}</span>
			</button>
		</p>
	{/if}
	</form>
	
	<script type="text/javascript">
		{literal}
			$(function() {
				setTimeout(function() {
					$('#payzen_form').submit();
				}, 1000);
			});
		{/literal}
	</script>
{/if}