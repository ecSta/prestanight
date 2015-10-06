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

<form method="POST" action="{$payzen_request_uri}" class="defaultForm form-horizontal">
	<input type="hidden" name="tabs" id="tabs" value="0" />
	
	<div style="width: 93%;">
		{$payzen_common}
	</div>
	
	<br /><br />
	
	<div class="tab-pane" id="tab-pane-1" style="width: 93%;">
		<div class="tab-page" id="step1">
		    <h4 class="tab" style="width: 200px !important;">{l s='GENERAL CONFIGURATION' mod='payzen'}</h4>
		    <br />
		    {$payzen_general_tab}
		</div>
		
		<div class="tab-page" id="step2">
		     <h4 class="tab" style="width: 200px !important;">{l s='ONE-TIME PAYMENT' mod='payzen'}</h4>
		     <br />
		     {$payzen_single_tab}
		</div>
		
		<div class="tab-page" id="step3">
		     <h4 class="tab" style="width: 200px !important;">{l s='PAYMENT IN SEVERAL TIMES' mod='payzen'}</h4>
		     <br />
		     {$payzen_multi_tab}		 
		</div>
	  
		<!--ONEY_ADMIN_START-->
		<div class="tab-page" id="step4">
		     <h4 class="tab" style="width: 200px !important;">{l s='FACILYPAY ONEY PAYMENT' mod='payzen'}</h4>
		     <br />
		     {$payzen_oney_tab}		 
		</div>
		<!--ONEY_ADMIN_END-->
	</div>
		
	<div class="clear" style="width: 93%;">
		<input type="submit" class="button" name="payzen_submit_admin_form" value="{l s='Save' mod='payzen'}" style="float: right;"/>
	</div>
		
	<script type="text/javascript">
		var pos_select = {$tabs};
		setupAllTabs();
	</script>
</form>