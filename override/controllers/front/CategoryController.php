<?php

class CategoryController extends CategoryControllerCore
{
	public function initContent() {
		parent::initContent();

		$this->setTemplate(_PS_THEME_DIR_.'category.tpl');

		if (!$this->customer_access)
			return;

		if (isset($this->context->cookie->id_compare))
			$this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));

		$this->productSort(); // Product sort must be called before assignProductList()

		$this->assignScenes();
		$this->assignSubcategories();
		$this->assignProductList();

		$aDescription = explode('<!-- pagebreak -->', $this->category->description);

		$this->context->smarty->assign(array(
			'category' => $this->category,
			// 'description_short' => Tools::truncateString($this->category->description, 350),
			'description_intro' => $aDescription[0],
			'description_outro' => count($aDescription) > 1 ? $aDescription[1] : [],
			'products' => (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null,
			'id_category' => (int)$this->category->id,
			'id_category_parent' => (int)$this->category->id_parent,
			'return_category_name' => Tools::safeOutput($this->category->name),
			'path' => Tools::getPath($this->category->id),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'categorySize' => Image::getSize(ImageType::getFormatedName('category')),
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			'thumbSceneSize' => Image::getSize(ImageType::getFormatedName('m_scene')),
			'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
			'allow_oosp' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
			'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
			'suppliers' => Supplier::getSuppliers(),
			'body_classes' => array($this->php_self.'-'.$this->category->id, $this->php_self.'-'.$this->category->link_rewrite)
		));
	}
}
