{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
		{if !isset($content_only) || !$content_only}
							</div><!-- #content -->
						</div><!-- #center_column -->

						{if isset($right_column_size) && !empty($right_column_size)}
						<div class="sidebar-content">
							<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column sidebar">{$HOOK_RIGHT_COLUMN}</div>
						</div>
						{/if}
					</div><!-- .row -->
				</div><!-- .container.main-content -->

				{if $page_name =='index'}
				<div id="content-bottom" class="parallax">
					<div class="container">
						{hook h='displayContentBottom'}
					</div>
				</div>
				{/if}
			</section ><!-- #columns -->

			<!-- Bottom-->
			{if $page_name =='index'}
			<section id="bottom">
				<div class="">
					{hook h='displayBottom'}
				</div>
			</section>
			{/if}

			{if isset($HOOK_FOOTER)}
			<!-- Footer -->
			<footer id="footer" class="hidden-print">
				{if $page_name|strpos:'module-leoblog' !== 0}
				<section id="pts-footer-top" class="footer-top parallax">
					<div class="container">
					<div class="inner">
						<div class="row">
							{if class_exists('PtsthemePanel')}
								<div class="footer-block col-lg-12 col-md-12 col-sm-12 col-xs-12">
									{plugin module='blocknewsletter' hook='footer'}
								</div>
							{/if}

							{hook h='displayFootertop'}
						</div>
					</div>
				</div>
				</section>

				<!-- Block CMS links -->
				<section id="pts-footercenter" class="footer-center">
					<div class="container"><div class="inner">
						<div class="row">
							<div class="footer-block block col-lg-3 col-md-3 col-sm-4 col-xs-12">
								<div class="staticontent-item">
									<img class="img-responsive" src="{$img_dir}logoNameOnly.png" alt="{$shop_name|escape:'html':'UTF-8'}" />

									<p class="social text-center">
										<a target="_blank" class="facebook" title="" href="https://www.facebook.com/so.nuit.fr" data-toggle="tooltip" data-placement="top" data-original-title="Facebook"> <em class="icon-facebook">&nbsp;</em></a>
										<a target="_blank" class="pinterest" title="" href="https://fr.pinterest.com/sonuit" data-toggle="tooltip" data-placement="top" data-original-title="Pinterest"> <em class="icon-pinterest">&nbsp;</em></a>
										<a target="_blank" class="google-plus" title="" href="https://plus.google.com/109631770212013358875" data-toggle="tooltip" data-placement="top" data-original-title="Google-plus"><em class="icon-google-plus">&nbsp;</em></a>
										<a target="_blank" class="rss" title="" href="http://sonuit.fr/modules/feeder/rss.php" data-toggle="tooltip" data-placement="top" data-original-title="Rss"> <em class="icon-rss">&nbsp;</em></a>
									</p>
								</div>
							</div>

							{$HOOK_FOOTER}
						</div>
					</div></div>
				</section>
				{/if}

				{*
				<section class="maplocal">
      				{hook h='displayMapLocal'}
	      			<div class="clearfix"></div>
    			</section>
    			*}

				<section id="powered">
					<div class="container"><div class="inner">
						<div class="row">
							<div id="pts-copyright" class="copyright">
								<div class="row">
									<div class="col-md-8 col-xs-12">
										{if isset($COPYRIGHT)&&$COPYRIGHT}
										<div class="copyright">{$COPYRIGHT}</div>
										{else}
										<p><span class="relative">
											Tous droits réservés
											<img class="flying tilting" src="{$img_dir}logoNoeudOnly.png" />
										</span></p>
										{/if}
										<p><span class="powered">© SoNuit - {date('Y')}</span></p>
									</div>

									{if $page_name|strpos:'module-leoblog' !== 0}
									<div class="col-md-4 col-xs-12">
										<ul id="payment_logos">
											<li class="col-xs-3 greyLayout"><a href="https://payzen.eu/paiement-securise" title="PayZen, 100% sécurisé" target="_blank"><img class="img-responsive" src="{$img_dir}paylogo_payzen_securise.png" alt="PayZen, 100% sécurisé" /></a></li>
											<li class="col-xs-3 greyLayout"><a href="{$link->getCMSLink(5, NULL)}"><img class="img-responsive" src="{$img_dir}paylogo_verified_by_visa.png" alt="Verified by VISA" /></a></li>
											<li class="col-xs-3 greyLayout"><a href="{$link->getCMSLink(5, NULL)}"><img class="img-responsive" src="{$img_dir}paylogo_mastercard_securecode.png" alt="MasterCard, SecureCode" /></a></li>
										</ul>
									</div>
									{/if}
								</div>
							</div>
							<div id="footer-bottom" class="pull-right">
								{hook h='displayFooterbottom'}
							</div>
						</div>
					</div></div>
				</section>
			</footer>
			{/if}
		</div> <!-- #page -->
		{/if}

		{include file="$tpl_dir./global.tpl"}
	</body>
</html>