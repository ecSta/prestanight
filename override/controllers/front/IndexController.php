<?php
/**
 * ABU EDIT
 */

class IndexController extends IndexControllerCore
{
	public function initContent()
	{
		$cats = Category::getHomeCategories($this->context->language->id, true);
		$this->context->smarty->assign(array(
			'cats' => $cats
		));
		parent::initContent();
	}
}