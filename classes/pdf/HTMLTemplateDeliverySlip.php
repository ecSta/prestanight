<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HTMLTemplateDeliverySlipCore extends HTMLTemplate
{
	public $order;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order($this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date);
		$prefix = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id);
		$this->title = sprintf(HTMLTemplateDeliverySlip::l('Delivery #%1$s%2$06d'), $prefix, $this->order_invoice->delivery_number);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME', null, null, (int)$this->order->id_shop);
		$path_logo = $this->getLogo();
		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->title,
			'date' => $this->date,
			'shop_name' => $shop_name,
			'width_logo' => 50, // ABU: force width, see: pdf\header.tpl
			'height_logo' => 'auto' // ABU: force height, see: pdf\header.tpl
		));

		return $this->smarty->fetch($this->getTemplate('header'));
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$delivery_address = new Address((int)$this->order->id_address_delivery);
		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');

		// if($this->order->id_address_delivery != $this->order->id_address_invoice) {
		// 	$invoice_address = new Address((int)$this->order->id_address_invoice);
		// 	$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		// }

		$shopData = array(
			'name'    => Configuration::get('PS_SHOP_NAME'),
			'addr1'   => Configuration::get('PS_SHOP_ADDR1'),
			'addr2'   => Configuration::get('PS_SHOP_ADDR2'),
			'city'    => Configuration::get('PS_SHOP_CITY'),
			'code'    => Configuration::get('PS_SHOP_CODE'),
			'country' => Configuration::get('PS_SHOP_COUNTRY'),
			'tva'     => Configuration::get('PS_SHOP_TVA'),
			'phone'   => Configuration::get('PS_SHOP_PHONE'),
			'email'   => Configuration::get('PS_SHOP_FAX'),
		);

		// $path_logo = $this->getLogo();
		// $width = 0;
		// $height = 0;
		// if (!empty($path_logo))
		// 	list($width, $height) = getimagesize($path_logo);
		// echo '<pre>';var_dump($width, $height, $path_logo);echo '</pre>';die;

		$carrier = new Carrier($this->order->id_carrier);
		$carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
		$this->smarty->assign(array(
			'order'            => $this->order,
			'order_details'    => $this->order_invoice->getProducts(),
			'delivery_address' => $formatted_delivery_address,
			'shopData'         => $shopData,
			'order_invoice'    => $this->order_invoice,
			'carrier'          => $carrier,
			'moreInfos'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin at nulla tellus. Aenean auctor tortor a ante varius blandit. Curabitur pretium dui vel vulputate maximus. Ut id gravida arcu. Nulla.',
		));

		// return $this->smarty->fetch($this->getTemplate('delivery-slip'));
		return $this->smarty->fetch($this->getTemplate('deliverySlip-sonuit'));
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'deliveries.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order->delivery_number).'.pdf';
	}
}

