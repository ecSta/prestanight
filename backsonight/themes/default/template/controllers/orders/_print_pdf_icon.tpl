{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{* Generate HTML code for printing Invoice Icon with link *}
<span class="btn-group-action">
	<span class="btn-group">
	{if Configuration::get('PS_INVOICE') && $order->invoice_number}
		<a class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id}">
			<i class="icon-file-text"></i>
		</a>
	{/if}
	{* Generate HTML code for printing Delivery Icon with link *}
	{if $order->delivery_number}
		<!-- <a class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id}"> -->
		<a class="btn btn-default _blank" data-toggle="modal" data-target="#deliveryInfo">
			<i class="icon-truck"></i>
		</a>
	{/if}
	</span>
</span>

{if Configuration::get('PS_INVOICE') && $order->invoice_number}
	<div class="pull-right" style="width:115px;">
		<input type="text" readonly class="text-right" onClick="this.select();" value="{$prefix}{'%06d'|sprintf:$order->invoice_number}" style="cursor:cell;" />
	</div>
{/if}


<!-- PLACE THIS SOMMEWHERE ELSE  -->
<div id="deliveryInfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="deliveryInfoLabel">Informations de livraison</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="deliveryMoreInfo" class="control-label">Complément d'information pour la livraison</label>
						<textarea id="deliveryMoreInfo" class="form-control" rows="5"></textarea>
					</div>
					<div class="form-group">
						<label for="deliveryReadyDate" class="control-label">Remise à quai</label>
						<input id="deliveryReadyDate" type="text" class="form-control">
					</div>
					<div class="form-group">
						<label for="deliveryWarehouse" class="control-label">D&eacute;p&ocirc;t</label>
						<input id="deliveryWarehouse" type="text" class="form-control">
					</div>
					<div class="form-group">
						<label for="deliveryOrigin" class="control-label">Provenance</label>
						<input id="deliveryOrigin" type="text" class="form-control">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
				<button type="button" class="btn btn-primary">Continuer</button>
			</div>
		</div>
	</div>
</div>
