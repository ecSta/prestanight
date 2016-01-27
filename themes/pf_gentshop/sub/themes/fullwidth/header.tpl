{*
* ABU
*}

<header id="header" class="header-v2">
    {if $page_name|strpos:'module-leoblog' !== 0}
	<section id="topbar" class="topbar-v2 col-md-12 col-sm-7 col-xs-4">
		<div class="container clearfix">
            {if $page_name =='index'}
			<h2>{l s='Bed, bedroom & customizable interior furnishings'}</h2>
			{/if}
			<div class="quick-access">
				{hook h="displayNav"}
				<div id="hotlineService" class="hidden-xs" title="{l s='From Monday to Friday, <br />10am to 6pm'}" data-toggle="tooltip" data-placement="bottom" data-container="body" data-html="true">
					<span class="hidden-md">{l s="Hotline"}&nbsp;</span>
					<i class="icon-phone"></i>&nbsp;04 22 13 20 83
				</div>
			</div>
		</div>
	</section>
	{/if}

	<div class="clearfix"></div>

	<section class="main-menu mainnav-v2">
		<div class="container">
			<div class="row">
				<div class="hidden col-md-3 col-sm-3 col-xs-12 logo inner">
					<div id="logo-theme-1" class="logo-store">
						{if Configuration::get('PTS_CP_LOGOTYPE') == 'logo-theme'}
						<div class="logo-theme">
							<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
								<img src="{$img_dir}logo_main.png" alt="SoNuit" width="115" />
							</a>
						</div>
						{else}
						<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
							<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
						</a>
						{/if}
					</div>
				</div>

				<div id="pts-mainnav" class="mainnav-v2 col-lg-6 col-md-9 col-sm-9 col-xs-10">
					{hook h="displayMainmenu"}
				</div>

				<div class="hidden-xs hidden-sm col-md-3 col-lg-2 col-lg-pull-1 logo inner">
					<div id="logo-theme" class="logo-store">
						{if Configuration::get('PTS_CP_LOGOTYPE') == 'logo-theme'}
						<div class="logo-theme">
							<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
								<img src="{$img_dir}logo_main.png" class="beating" alt="SoNuit" width="115" />
							</a>
						</div>
						{else}
						<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
							<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
						</a>
						{/if}
					</div>
				</div>

				<div class="col-lg-6 quick-action pull-right">
					{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
				</div>
			</div>
		</div>
	</section>
</header>