<?php
/**
 * ABU EDIT
 */

class IndexController extends IndexControllerCore
{
	public $php_self = 'index';

	public function initContent() {
		global $id_lang;

		$cats = Category::getHomeCategories($id_lang);

		parent::initContent();
	}
}