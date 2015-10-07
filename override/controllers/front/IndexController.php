<?php
/**
 * ABU EDIT
 */

class IndexController extends IndexControllerCore
{
	public function initContent()
	{
		$cats = Category::getHomeCategories($this->context->language->id);
		$this->context->smarty->assign(array(
			'cats' => $cats
		));
		parent::initContent();
	}
}