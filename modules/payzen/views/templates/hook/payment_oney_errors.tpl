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
	<a class="unclickable">
		<img class="logo" src="{$base_dir_ssl}modules/payzen/views/images/BannerLogo3.png" alt="PayZen"/>
		
		<span style="color: red; display: inline-block; vertical-align: middle;">
		{foreach from=$payzen_oney_errors item="error"}
			{$error} <br />
		{/foreach}
		</span>
	</a>
</div>