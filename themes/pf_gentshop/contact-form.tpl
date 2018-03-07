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

{capture name=path}{l s='Contact'}{/capture}
<h1 class="page-heading bottom-indent">
    {l s='Customer service'} - {if isset($customerThread) && $customerThread}{l s='Your reply'}{else}{l s='Contact us'}{/if}
</h1>
{if isset($confirmation)}
	<p class="alert alert-success">{l s='Your message has been successfully sent to our team.'}</p>
	<ul class="footer_links clearfix">
		<li>
            <a class="btn btn-default button button-small" href="{$base_dir}">
                <span>
                    <i class="icon-chevron-left"></i>{l s='Home'}
                </span>
            </a>
        </li>
	</ul>
{elseif isset($alreadySent)}
	<p class="alert alert-warning">{l s='Your message has already been sent.'}</p>
	<ul class="footer_links clearfix">
		<li>
            <a class="btn btn-default button button-small" href="{$base_dir}">
                <span>
                    <i class="icon-chevron-left"></i>{l s='Home'}
                </span>
            </a>
        </li>
	</ul>
{else}
	{include file="$tpl_dir./errors.tpl"}

    {*ABU edit: ajout du ptit text HELP > Sur-mesure for dummies*}
    {if $surmesure}
    <div class="well">
        <div class="page-header">
            <h3>&laquo; Sur-mesure &raquo;</h3>
        </div>
            <p>
                Vous souhaitez composer une chambre originale, rien sur le marché ne correspond vraiment à vos besoins et envies ? Cette rubrique « sur-mesure » est faite pour vous.<br />
                Nous allons vous accompagner dans la création de votre ​aménagement idéal, chambre, dressing, bibliothèque ...
            </p>
            <p>Plusieurs de nos ​partenaires fabricants proposent des collections de meubles « à la carte », ce qui vous laisse donc une grande souplesse tant en terme d’aménagements, de matériaux ​que de couleurs.</p>
            <p>
                Vous pouvez déjà vous inspirer des compositions proposées avec des dimensions préétablies, et accompagnées d’un chiffrage.<br />
                Chaque composition est une association ​esthétique et ergonomique ​de différents meubles et panneaux, ​qui seront confectionnés ​selon les options (couleurs, poignées, dimensions​, etc.. ​) que vous aurez choisies​.​</p>
            <p>
                ​Votre composition sur-mesure fera donc l’objet d​'un nouveau plan​ avec un ​nouveau chiffrage.<br />
                Vous trouverez tous les détails dans votre nouvelle fiche produit, qui vous sera envoyée spécialement par votre concepteur SONUIT pour répondre à vos besoins.<br />
                ​Vous pourrez ainsi valider votre commande avec votre nouvelle conception personnalisée.​
            </p>
    </div>
    {/if}

	<form action="{$request_uri}" method="post" class="contact-form-box" enctype="multipart/form-data">
        <div id="contactForm_hotline" class="panel panel-default">
            <div class="panel-heading">
                <i class="icon-phone icon-fw icon-2x"></i>
                <span class="text-2x">+33 (0)4 22 13 20 83</span>
            </div>

            <div class="panel-body">
                <p>{l s='Get in touch with our conceptors from monday to friday, 10am to 6pm'}.</p>
            </div>
        </div>

		<fieldset>
        <h3 class="page-subheading">{l s='send a message'}</h3>
        <div class="clearfix row">
            <div class="col-xs-12 col-md-3">
                <div class="form-group selector1">
                    <label for="id_contact">{l s='Subject Heading'}</label>
                {if isset($customerThread.id_contact)}
                        {foreach from=$contacts item=contact}
                            {if $contact.id_contact == $customerThread.id_contact}
                                <input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contact.name|escape:'html':'UTF-8'}" readonly="readonly" />
                                <input type="hidden" name="id_contact" value="{$contact.id_contact}" />
                            {/if}
                        {/foreach}
                    </div>
                {else}
                    <select id="id_contact" class="form-control" name="id_contact"{if $surmesure || $surdemande} disabled="disabled"{/if}>
                        <option value="0">{l s='-- Choose --'}</option>
                        {foreach from=$contacts item=contact}
                            <option value="{$contact.id_contact|intval}" {if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
                    <p id="desc_contact0" class="desc_contact">&nbsp;</p>
                    {foreach from=$contacts item=contact}
                        <p id="desc_contact{$contact.id_contact|intval}" class="desc_contact contact-title" style="display:none;">
                            <i class="icon-comment-alt"></i>{$contact.description|escape:'html':'UTF-8'}
                        </p>
                    {/foreach}
                {/if}
                <p class="form-group">
                    <label for="email">{l s='Email address'}</label>
                    {if isset($customerThread.email)}
                        <input class="form-control grey" type="text" id="email" name="from" value="{$customerThread.email|escape:'html':'UTF-8'}" readonly="readonly" />
                    {else}
                        <input class="form-control grey validate" type="text" id="email" name="from" data-validate="isEmail" value="{$email|escape:'html':'UTF-8'}" />
                    {/if}
                </p>
                {if !$PS_CATALOG_MODE}
                    {if (!isset($customerThread.id_order) || $customerThread.id_order > 0)}
                        <div class="form-group selector1">
                            <label>{l s='Order reference'}</label>
                            {if !isset($customerThread.id_order) && isset($is_logged) && $is_logged}
                                <select name="id_order" class="form-control"{if $surmesure || $surdemande} disabled="disabled"{/if}>
                                    <option value="0">{l s='-- Choose --'}</option>
                                    {if !$surmesure || !$surdemande}
                                        {foreach from=$orderList item=order}
                                            <option value="{$order.value|intval}"{if $order.selected|intval} selected="selected"{/if}>{$order.label|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            {elseif !isset($customerThread.id_order) && empty($is_logged)}
                                <input class="form-control grey" type="text" name="id_order" id="id_order" value="{if isset($customerThread.id_order) && $customerThread.id_order|intval > 0}{$customerThread.id_order|intval}{else}{if isset($smarty.post.id_order) && !empty($smarty.post.id_order)}{$smarty.post.id_order|escape:'html':'UTF-8'}{/if}{/if}" />
                            {elseif $customerThread.id_order|intval > 0}
                                <input class="form-control grey" type="text" name="id_order" id="id_order" value="{if isset($customerThread.reference) && $customerThread.reference}{$customerThread.reference|escape:'html':'UTF-8'}{else}{$customerThread.id_order|intval}{/if}" readonly="readonly" />
                             {/if}
                        </div>
                    {/if}
                    {if isset($is_logged) && $is_logged}
                        <div class="form-group selector1">
                            <label class="unvisible">{l s='Product'}</label>
                            {if !isset($customerThread.id_product)}
                                {foreach from=$orderedProductList key=id_order item=products name=products}
                                    <select name="id_product" id="{$id_order}_order_products" class="unvisible product_select form-control"{if !$smarty.foreach.products.first} style="display:none;"{/if}{if !$smarty.foreach.products.first} disabled="disabled"{/if}>
                                        <option value="0">{l s='-- Choose --'}</option>
                                        {foreach from=$products item=product}
                                            <option value="{$product.value|intval}">{$product.label|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                {/foreach}
                            {elseif $customerThread.id_product > 0}
                                <input  type="hidden" name="id_product" id="id_product" value="{$customerThread.id_product|intval}" readonly="readonly" />
                             {/if}
                        </div>
                    {/if}
                {/if}
                {if $fileupload == 1}
                    <p class="form-group">
                        <label for="fileUpload">{l s='Attach File'}</label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
                        <input type="file" name="fileUpload" id="fileUpload" />
                    </p>
                {/if}
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="form-group">
                    <label for="message">{l s='Message'}</label>
                    <textarea class="form-control" id="message" name="message">{if isset($message)}{$message|escape:'html':'UTF-8'|stripslashes}{/if}</textarea>
                </div>
            </div>
        </div>
        <div class="g-recaptcha" data-sitekey="6LcLKkoUAAAAADyyzD0XEPuWNtvliqHY1gv_hz7m" data-callback="v"></div>
        <div class="submit">
            <button type="submit" name="submitMessage" id="submitMessage" class="btn btn-outline-default" disabled="disabled"><span>{l s='Send'}</span></button>
		</div>
	</fieldset>
</form>

<script type="text/javascript">var v = function(response) { $('#submitMessage').prop('disabled',false);};</script>
{/if}
{addJsDefL name='contact_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
