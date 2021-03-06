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
<!DOCTYPE HTML>

<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$language_code|escape:'html':'UTF-8'}"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$language_code|escape:'html':'UTF-8'}"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$language_code|escape:'html':'UTF-8'}"><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$language_code|escape:'html':'UTF-8'}"><![endif]-->

<html lang="{$language_code|escape:'html':'UTF-8'}" dir="{$LANG_DIRECTION}" class="{$LANG_DIRECTION}">
    <head>
        <meta charset="utf-8" />
        <title>{$meta_title|escape:'html':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
        <meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
        <meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
{/if}
        <meta name="generator" content="PrestaShop" />
        <meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
        <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
        <link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
{if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
        {if preg_match("#global#",$css_uri)}
        <link rel="stylesheet" href="{$css_uri}"  id="global-style" type="text/css" media="{$media}" />
        {else}
            <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
        {/if}
    {/foreach}
{/if}


{* ABU : custom CSS file *}
    <link rel="stylesheet" href="/themes/pf_gentshop/css/custom-fullwidth.css?" type="text/css" media="all" />
{* /ABU : custom Css file *}


{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
    {$js_def}
    {foreach from=$js_files item=js_uri}
    <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
    {/foreach}
{/if}
        {$HOOK_HEADER}

        <!--[if IE 8]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="keep-header {if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso} layout-{$DEFAUTL_LAYOUT}" itemscope="" itemtype="http://schema.org/WebPage" >
    {if !isset($content_only) || !$content_only}
        {if isset($restricted_country_mode) && $restricted_country_mode}
            <div id="restricted-country">
                <p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span></p>
            </div>
        {/if}
        <section class="banner hidden-xs hidden-sm">
            <div class="container">
                {hook h="displayBanner"}
            </div>
        </section>

        <div id="page">
            {if file_exists($THEME_HEADER_FILE)}
                {include file=$THEME_HEADER_FILE}
            {else}

            <header id="header" class="header-v1">
                <section id="topbar" class="topbar-v1">
                    <div class="container clearfix">
                        <div class="quick-access">
                            {hook h="displayNav"}
                        </div>
                        <div class="quick-action">
                            {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}

                        </div>
                    </div>
                </section>

                <section class="main-menu mainnav-v1">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 logo inner">
                                <div id="logo-theme" class="logo-store">
                                {if Configuration::get('PTS_CP_LOGOTYPE') == 'logo-theme'}
                                    <div class="logo-theme">
                                        <a href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
                                            <img src="{$img_dir}logo-theme-white.png" alt="logo"/>
                                        </a>
                                    </div>
                                {else}
                                    <a href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
                                            <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                                    </a>

                                {/if}
                                </div>
                            </div>

                            <div id="pts-mainnav" class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                {hook h="displayMainmenu"}
                            </div>
                        </div>
                    </div>
                </section>

            </header>
            {/if}

            {if $page_name =='index'}
            <section  id="pts-slideshow" class="slideshow hidden-xs">
                {hook h="displayslideshow"}
            </section>

            {* Categories' image + link to *}
            <section id="abu_categories" class="row abu-5-col">
                {foreach from=$cats item=cat name=categories}
                    <div class="cols col-xs-12">
                        <div class="relative">
                            <a alt="{$cat.name}" href="{$link->getCategoryLink($cat.id_category, null)|escape:'html':'UTF-8'}" title="{$cat.name}">
                                <div class="block_content text-center">
                                    <img class="img-responsive" alt="{$cat.name}" src="{$img_dir}img_categories/so-{$cat.id_category}.jpg" />
                                </div>
                            </a>
                        </div>
                    </div>
                {/foreach}
            </section>

            {* Text including wtf is SoNuit *}
            <section id="abu_what_is_so_nuit" class="row">
                <div class="well">
                    <div class="container text-center">
                        <h1>SONUIT : lit, chambre  et mobilier d’interieur personnalisable</h1><hr />
                        <p>Avec SONUIT, créez l’intérieur qui vous ressemble, aux couleurs et aux dimensions qui mettrons en valeur votre foyer.</p>
                        <p>Vous trouverez du mobilier de chambre pour toute la famille : lit, lit evolutif, lit mezzanine, lit superposé, lit gigogne, armoire, bureau, chambre sur mesure, bibliothèque et dressing sur mesure, tapis, fauteuil, luminaire et autres accessoires d’intérieur.</p>
                        <p>Venez découvrir notre collection de mobilier personnalisable.<br />Choisissez les couleurs et dimensions de votre lit, les tissus de votre fauteuil, les accessoires de votre dressing sur mesure, la finition de votre armoire, l’agencement de la chambre de votre enfant. Créez LA pièce originale et unique.</p>
                        <p>Vitaminez votre intérieur en quelques clics…<br />Sur sonuit.fr, on profite pleinement des services de conception, des conseils en agencement, des astuces gain de place, des couleurs pétillantes, des lignes épurées, ainsi que des tendances émergentes… De quoi en séduire plus d’un!</p>
                    </div>
                </div>
            </section>
            {/if}

            {if $page_name =='index'}
            <section id="top_column" class="top_column" >
                <div class="container">
                    <div class="row">
                        <div >{hook h="displayTopColumn"}</div>
                    </div>
                </div>
            </section>

            <section id="pts-promotion" class="promote_top">
                <div class="wrap">
                    <div class="container">
                        <div class="row">
                            {hook h="displayPromoteTop"}
                        </div>
                    </div>
                </div>
            </section>
            {/if}

            <section id="columns" class="offcanvas-siderbars">
            {if $page_name !='index' && $page_name !='pagenotfound'}
                {include file="$tpl_dir./breadcrumb.tpl"}
            {/if}
                <div class="container main-content">
                        <div class="row">
                            {if isset($left_column_size) && !empty($left_column_size)}
                            <div id="left_column" class="sidebar column col-xs-12 col-sm-12 col-md-{$left_column_size|intval} col-lg-{$left_column_size|intval} offcanvas-sidebar">
                                <div class="sidebar-content">
                                    {$HOOK_LEFT_COLUMN}
                                </div>
                            </div>
                            {/if}
                        {if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
                        <div id="center_column" class="center_column col-xs-12 col-sm-12 col-md-{$cols|intval} col-lg-{$cols|intval}">
                                    <div id="content">

    {/if}