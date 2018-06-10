{**
 * THEME FILE : GENTSHOP
**}

{include file="$tpl_dir./errors.tpl"}

{if !isset($errors) OR !sizeof($errors)}
	<p class="page-heading product-listing">{$manufacturer->name|escape:'html':'UTF-8'}</p>
	{if !empty($manufacturer->description) || !empty($manufacturer->short_description)}
		<div class="description_box rte">
			{if !empty($manufacturer->short_description)}
				<div class="short_desc">
					{$manufacturer->short_description}
				</div>
				<div class="hide_desc">
					{$manufacturer->description}
				</div>
				<a href="#" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide();$(this).prev().prev().hide(); return false;">
					{l s='More'}
				</a>
			{else}
				<div>
					{$manufacturer->description}
				</div>
			{/if}
		</div>
	{/if}

	{if $products}
	<div class="content_sortPagiBar clearfix">
    	<div class="sortPagiBar clearfix col-xs-8 col-sm-9 col-md-10">
    		{include file="./product-sort.tpl"}
			<div class="hidden-xs hidden-sm hidden-md hidden-lg">{include file="./nbr-product-page.tpl"}</div>
		</div>
        <div class="top-pagination-content clearfix col-xs-4 col-sm-3 col-md-2">
        	{include file="./product-compare.tpl"}
        </div>
	</div>
	{include file="./product-list.tpl" products=$products}
	<div class="bottom-pagination-content content_sortPagiBar col-xs-12 col-sm-12 clearfix">
	{include file="./product-compare.tpl"}
            {include file="./pagination.tpl" paginationId='bottom'}
		</div>
	{else}
		<p class="alert alert-warning">{l s='No products for this manufacturer.'}</p>
	{/if}
{/if}