<?php
/**
 * ABU EDIT
 */

class IndexController extends IndexControllerCore
{
	public $php_self = 'index';

	public function initContent() {

		$cats = Category::getHomeCategories($id_lang);
		var_dump($cats);

		parent::initContent();
	}
}