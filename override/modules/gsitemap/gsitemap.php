<?php

class GsitemapOverride extends Gsitemap {


	/**
	 * Hydrate $link_sitemap with products link
	 *
	 * ABU: DO NOT RETRIEVE PRODUCTS (EVEN IF ACTIVE) FROM A NON-ACTIVE CATEGORY
	 *
	 * @param array  $link_sitemap contain all the links for the Google Sitemap file to be generated
	 * @param string $lang         language of link to add
	 * @param int    $index        index of the current Google Sitemap file
	 * @param int    $i            count of elements added to sitemap main array
	 * @param int    $id_product   product object identifier
	 *
	 * @return bool
	 */
	protected function _getProductLink(&$link_sitemap, $lang, &$index, &$i, $id_product = 0)
	{
		$link = new Link();
		if (method_exists('ShopUrl', 'resetMainDomainCache'))
			ShopUrl::resetMainDomainCache();

		$products_id = Db::getInstance()->ExecuteS("
			SELECT p.id_product
			FROM "._DB_PREFIX_."product_shop p
			JOIN "._DB_PREFIX_."category c ON c.id_category = p.id_category_default
			WHERE p.id_product >= ". intval($id_product) ."
				AND p.active = 1
				AND p.visibility != 'none'
				AND p.id_shop = ". $this->context->shop->id ."
				AND c.active = 1
			ORDER BY p.id_product ASC");

		foreach ($products_id as $product_id)
		{
			$product = new Product((int)$product_id['id_product'], false, (int)$lang['id_lang']);

			$url = $link->getProductLink($product, $product->link_rewrite, htmlspecialchars(strip_tags($product->category)), $product->ean13, (int)$lang['id_lang'], (int)$this->context->shop->id, 0, true);

			$id_image = Product::getCover((int)$product_id['id_product']);
			if (isset($id_image['id_image']))
			{
				$image_link = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.(int)$id_image['id_image'], 'large_default');
				$image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
					array(
						'https',
						Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri
					), array(
						'http',
						Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.Context::getContext()->shop->virtual_uri
					), $image_link
				) : $image_link;
			}
			$file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
			$image_product = array();
			if (isset($image_link) && ($file_headers[0] != 'HTTP/1.1 404 Not Found' || $file_headers === true))
				$image_product = array(
					'title_img' => htmlspecialchars(strip_tags($product->name)),
					'caption' => htmlspecialchars(strip_tags($product->description_short)),
					'link' => $image_link
				);
			if (!$this->_addLinkToSitemap(
				$link_sitemap, array(
					'type' => 'product',
					'page' => 'product',
					'lastmod' => $product->date_upd,
					'link' => $url,
					'image' => $image_product
				), $lang['iso_code'], $index, $i, $product_id['id_product']
			))
				return false;

			unset($image_link);
		}

		return true;
	}

}
