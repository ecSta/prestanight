<?php

class Image extends ImageCore {

	/**
	 * Return first image (by position) associated with a product attribute
	 *
	 * @param int $idShop             Shop ID
	 * @param int $idLang             Language ID
	 * @param int $idProduct          Product ID
	 * @param int $idProductAttribute Product Attribute ID
	 *
	 * @return array
	 */
	public static function getBestImageAttribute($idShop, $idLang, $idProduct, $idProductAttribute) {
		$cacheId = 'Image::getBestImageAttribute'.'-'.(int) $idProduct.'-'.(int) $idProductAttribute.'-'.(int) $idLang.'-'.(int) $idShop;
		if (!Cache::isStored($cacheId)) {
			$row = Db::getInstance()->getRow('
					SELECT image_shop.`id_image` id_image, il.`legend`
					FROM `'._DB_PREFIX_.'image` i
					INNER JOIN `'._DB_PREFIX_.'image_shop` image_shop
						ON (i.id_image = image_shop.id_image AND image_shop.id_shop = '.(int) $idShop.')
						INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai
						ON (pai.`id_image` = i.`id_image` AND pai.`id_product_attribute` = '.(int) $idProductAttribute.')
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $idLang.')
					WHERE i.`id_product` = '.(int) $idProduct.' ORDER BY i.`position` ASC');
			Cache::store($cacheId, $row);
		} else {
			$row = Cache::retrieve($cacheId);
		}
		return $row;
	}
}