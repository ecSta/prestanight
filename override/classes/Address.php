<?php

/*
** Zip Code Zone
** MARICHAL Emmanuel
** emmanuel.marichal@gmail.com
*/

class Address extends AddressCore
{
	public static function getZoneById($id_address)
	{
		$address = new Address((int)$id_address);

		$zone = Db::getInstance()->getValue('
		SELECT id_zone
		FROM '._DB_PREFIX_.'zip_code_zone
		WHERE id_country = '.(int)$address->id_country.'
		AND min <= '.(int)$address->postcode.' AND max >= '.(int)$address->postcode);

		return $zone ? (int)$zone : (int)parent::getZoneById((int)$id_address);
	}
}