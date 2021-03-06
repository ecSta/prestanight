
<div class="product-block {if Configuration::get('PTS_CP_PRODUCT_LAYOUT') == 'gallery' && isset($product.ptsimages)}gallery {/if}" itemscope="" itemtype="http://schema.org/Product"><div class="product-container">
	{hook h='displayProductListGallery' product=$product}
		<div class="product-image-container image swap">
				{* ABU edit : osef message new *}
				{*if isset($product.new) && $product.new == 1}
					<span class="product-label product-label-special new-box">
						<span class="new-label">{l s='New'}</span>
					</span>
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
					<span class="product-label product-label-special sale-box">
						<span class="sale-label">{l s='Sale!'}</span>
					</span>
				{/if*}

				<a class="img product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" itemprop="url">
					<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
				</a>

				{hook h='displayProductListSwap' product=$product}

				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="content_price price price-quick">

						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							{*ABU edit: osef old price*}
							{*if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								<span class="old-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>

							{/if*}
							<span itemprop="price" class="product-price">
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							<meta itemprop="priceCurrency" content="{$priceDisplay}" />
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}

								{if $product.specific_prices.reduction_type == 'percentage'}
								<span class="content_price_percent sale-percent-box" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
									<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
								</span>
								{/if}
							{/if}
						{/if}
					</div>
				{/if}

				<div class="right">
					<div class="action hidden-xs">
						<div>
							{*<div class="col-lg-offset-6 col-lg-6 col-md-offset-3 col-md-9 col-xs-offset-3 col-xs-9 btn-action">*}
							<div class="col-lg-offset-4 col-lg-8 text-center col-xs-10 btn-action">
								<div class="zoom">
									<a class="info-view colorbox product-zoom btn-tooltip pts-fancybox cboxElement" href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}" rel="nofollow" data-toggle="tooltip" title="{l s='zoom'}"> </a>
								</div>
								{if isset($comparator_max_item) && $comparator_max_item}
									<div class="compare">
										<a class="btn-tooltip add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" data-toggle="tooltip" title="{l s='Compare'}"> </a>
									</div>
								{/if}
								{hook h='displayProductListFunctionalButtons' product=$product}
								{if isset($quick_view) && $quick_view}
									<div class="quick-view">
										<a class="quick-view btn-tooltip pts-colorbox cboxElement" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" data-toggle="tooltip" title="{l s='Quick view'}">
											{*<span class="hidden-md hidden-sm hidden-xs">{l s='Quick view'}</span>*}
										</a>
									</div>
								{/if}
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="product-meta">
				<div class="left">
					{*ABU edit: plus de rating... pour l'instant*}
					{*hook h='displayProductListReviews' product=$product*}
					<h3 class="name" itemprop="name">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
							{*ABU edit: apparemment pas assez long qu'ils disent...*}
							{*$product.name|truncate:65:'...'|escape:'html':'UTF-8'*}
							{$product.name|escape:'html':'UTF-8'}
						</a>
					</h3>
					<div class="product-desc description" itemprop="description">
						{$product.description_short|strip_tags:'UTF-8'|truncate:200:'...'}
					</div>

					{*if isset($product.color_list)}
						<div class="color-list-container product-colors">{$product.color_list} </div>
					{/if*}


					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="discount">{l s='Reduced price!'}</span>
							{/if}
					</div>
					{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="availability">
								{if ($product.allow_oosp || $product.quantity > 0)}
									<span class="{if $product.quantity <= 0 && !$product.allow_oosp}out-of-stock{else}available-now{/if}">
										<link itemprop="availability" href="http://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
									</span>
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="available-dif">
										<link itemprop="availability" href="http://schema.org/LimitedAvailability" />{l s='Product available with different options'}
									</span>
								{else}
									<span class="out-of-stock">
										<link itemprop="availability" href="http://schema.org/OutOfStock" />{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}


					<div class="bottom">
						<div class="wrap-hover">
							{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
								{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
									{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
									<div class="addtocart cart">
										<a data-toggle="tooltip"  class="btn btn-shopping-cart btn-outline-default ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
											<span class="hidden-md hidden-sm hidden-xs">{l s='Add to cart'}</span>
											<span class="hidden-lg"><i class="icon-shopping-cart"></i></span>
										</a>
									</div>


								{else}
									<div class="addtocart cart"><span data-toggle="tooltip"  class="btn btn-shopping-cart btn-outline-default ajax_add_to_cart_button disabled">
										<i class="icon-shopping-cart"></i>
										<span>{l s='Add to cart'}</span>
									</span></div>
								{/if}
							{/if}

							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="content_price price">

								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
										{hook h="displayProductPriceBlock" product=$product type="old_price"}
										<span class="old-price">
											{displayWtPrice p=$product.price_without_reduction}
										</span>
										&emsp;
										{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}

									{/if}
									<span itemprop="price" class="product-price">
										{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									</span>
									<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
								{/if}

							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
							</div>

							{/if}
						</div>
					</div>
				</div>
			</div>
		</div><!-- .product-container> --></div>
<script type="text/javascript">
	$("a.pts-fancybox").fancybox();
</script>