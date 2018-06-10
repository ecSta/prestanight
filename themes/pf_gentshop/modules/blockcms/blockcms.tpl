{**
 * THEME FILE : GENTSHOP
**}

{if $block == 1}
	<!-- Block CMS module -->
	{foreach from=$cms_titles key=cms_key item=cms_title}
		<section id="informations_block_left_{$cms_key}" class="block informations_block_left nopadding">
			<h4 class="title_block">
				<span>
					{if !empty($cms_title.name)}{$cms_title.name}{else}{$cms_title.category_name}{/if}
				</span>
			</h4>
			<div class="block_content">
				<ul class="list-block ">
					{foreach from=$cms_title.categories item=cms_page}
						{if isset($cms_page.link)}
							<li class="bullet">
								<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.name|escape:'html':'UTF-8'}" rel="nofollow">
									{$cms_page.name|escape:'html':'UTF-8'}
								</a>
							</li>
						{/if}
					{/foreach}
					{foreach from=$cms_title.cms item=cms_page}
						{if isset($cms_page.link)}
							<li>
								<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.meta_title|escape:'html':'UTF-8'}" rel="nofollow">
									{$cms_page.meta_title|escape:'html':'UTF-8'}
								</a>
							</li>
						{/if}
					{/foreach}
					{if $cms_title.display_store}
						<li>
							<a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='Our stores' mod='blockcms'}" rel="nofollow">
								{l s='Our stores' mod='blockcms'}
							</a>
						</li>
					{/if}
				</ul>
			</div>
		</section>
	{/foreach}
	<!-- /Block CMS module -->
{else}
	<!-- MODULE Block footer -->

	<div class="footer-block block block_various_links col-lg-3 col-md-3 col-sm-4 col-xs-12">
		<div class="title_block">{l s='Customer Service' mod='blockcms'}</div>
		<ul class="toggle-footer list-group list-block">

			{if $display_stores_footer}
				<li class="item">
					<a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='Our stores' mod='blockcms'}" rel="nofollow">
						{l s='Our stores' mod='blockcms'}
					</a>
				</li>
			{/if}
			{if $show_contact}
			<li class="item">
				<a href="{$link->getPageLink($contact_url, true)|escape:'html':'UTF-8'}" title="{l s='Contact us' mod='blockcms'}" rel="nofollow">
					{l s='Contact us' mod='blockcms'}
				</a>
			</li>
			{/if}
			{if $show_sitemap}
			<li>
				<a href="{$link->getPageLink('sitemap')|escape:'html':'UTF-8'}" title="{l s='Sitemap' mod='blockcms'}" rel="nofollow">
					{l s='Sitemap' mod='blockcms'}
				</a>
			</li>
			{/if}
	        <li>
                <a href="{$link->getPageLink('supplier')|escape:'html':'UTF-8'}" title="{l s='View a list of suppliers'}" rel="nofollow">
                    {l s='Suppliers' mod='blockcms'}
                </a>
            </li>
		</ul>
	</div>

	<div class="footer-block block block_various_links col-lg-3 col-md-3 col-sm-4 col-xs-12">
		<div class="title_block">{l s='Information' mod='blockcms'}</div>
		<ul class="toggle-footer list-group list">
			{foreach from=$cmslinks item=cmslink}
				{if $cmslink.meta_title != ''}
					<li class="item">
						<a href="{$cmslink.link|escape:'html':'UTF-8'}" title="{$cmslink.meta_title|escape:'html':'UTF-8'}" rel="nofollow">
							{$cmslink.meta_title|escape:'html':'UTF-8'}
						</a>
					</li>
				{/if}
			{/foreach}

		</ul>
	</div>
	<!-- /MODULE Block footer -->
{/if}
