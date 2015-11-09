	{if !empty($ptsmanufacturers )}
		<div id="ptsblockmanufacturer" class="block ptsblockmanufacturer carousel slide">
			{if $show_title}
				<h4 class="title_block nostyle">{l s='Our Brand' mod='ptsblockmanufacturer'}</h4>
			{/if}

			<div class="block_content">
				{$tabname="ptsblockmanufacturer"}
				{if !empty($ptsmanufacturers)}
					<div id="{$tabname}">
						{*ABU edit: no more carousel*}
						{*if count($ptsmanufacturers) > $manuf_page}
							<div class="carousel-controls">
								<a class="carousel-control left" href="#{$tabname}" data-slide="prev">&lsaquo;</a>
								<a class="carousel-control right" href="#{$tabname}" data-slide="next">&rsaquo;</a>
							</div>
						{/if*}

						{*<div class="carousel-inner">
							{$ptsmanufacterer = array_chunk($ptsmanufacturers, $manuf_page)}
							{foreach from=$ptsmanufacterer item=ptsmanufacturers name=mypLoop}
								<div class="item {if isset($active) && $active == 1} active{/if} item {if $smarty.foreach.mypLoop.first}active{/if}">
									{foreach from=$ptsmanufacturers item=manuf name=ptsmanufacturer}
										<div class="col-xs-6 col-sm-3 col-md-2 col-lg-1">
											<div class="block_manuf clearfix">
												{if $manuf.linkIMG}
													<div class="blog-image">
														<a href="{$manuf.link|escape:'html':'UTF-8'}">
															<img class="img-responsive" src="{$manuf.linkIMG}" alt="{$manuf.name}" />
														</a>
													</div>
												{/if}
											</div>
										</div>
									{/foreach}
								</div>
							{/foreach}
						</div>*}


						{*ABU: nouvel arrangement des items - suppression du carousel*}

						{assign var="manuRows" value=(($ptsmanufacturers|count)/$manuf_page)|intval}
						{assign var="manuRest" value=($ptsmanufacturers|count)%$manuf_page}
						{$ptsmanufacterer = array_chunk($ptsmanufacturers, $manuf_page)}

						{foreach from=$ptsmanufacterer item=ptsmanufacturers name=manufLoop}
							<div class="row row-{$smarty.foreach.manufLoop.index}">
								{if $smarty.foreach.manufLoop.index == $manuRows && ($manuRest%2) != 0}<div class="col-xs-12 col-xs-offset-{$manuf_page-$manuRest}">{/if}
								{foreach from=$ptsmanufacturers item=manuf name=ptsmanufacturer}
									<div class="col-xs-6 col-sm-4 col-md-2{if $smarty.foreach.manufLoop.index == $manuRows && ($manuRest%2) == 0 && $smarty.foreach.ptsmanufacturer.index == 0} col-md-offset-{$manuf_page-$manuRest}{/if}">
										<h5 class="block_manuf clearfix" data-toggle="tooltip" data-placement="top" title="{$manuf.name}">
											{if $manuf.linkIMG}
												<div class="blog-image">
													{*ABU: desactive les link en attendant resolution MySQL query stop during execution*}
													<a href="{$manuf.link|escape:'html':'UTF-8'}" title="{$manuf.name}">
														<img class="img-responsive" src="{$manuf.linkIMG}" alt="{$manuf.name}" />
													</a>
												</div>
											{/if}
										</h5>
									</div>
								{/foreach}
								{if $smarty.foreach.manufLoop.index == $manuRows && ($manuRest%2) != 0}</div>{/if}
							</div>
						{/foreach}
					</div>
				{/if}
			</div>
		</div>
	{/if}
<!-- /MODULE Block ptsblockmanufacturer -->

{*ABU edit: no more carousel*}
{*literal}
<script type="text/javascript">
$(document).ready(function() {
  $('#{/literal}{$tabname}'{literal}).each(function(){
      $(this).carousel({
          pause: 'hover',
          interval: {/literal}{$interval}{literal}
      });
  });
});
</script>
{/literal*}