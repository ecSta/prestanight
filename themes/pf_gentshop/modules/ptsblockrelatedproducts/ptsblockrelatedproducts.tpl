<!-- MODULE Block ptsblockrelatedproducts -->
{if $products|@count gt 0 }
<div id="relatedproducts" class="block no-print products_block exclusive ptsblockrelatedproducts carousel">
		<h3 class="title_block">{$products|@count} {l s='other products in the same category' mod='ptsblockrelatedproducts'}</h3>
		<div class="block_content">
			{if !empty($products )}
				{$tabname="ptsblockrelatedproducts"}
				{include file="$tpl_dir./sub/products_module.tpl" modulename='ptsblockrelatedproducts'}
			{/if}
		</div>
</div>
{/if}

<!-- /MODULE Block ptsblockrelatedproducts -->
<script>
$(document).ready(function() {
    $('{$tabname}').each(function(){
        $(this).carousel({
            pause: false,
            interval: 3000
        });
    });
});
</script>
