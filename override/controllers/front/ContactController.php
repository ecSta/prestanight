<?php

class ContactController extends ContactControllerCore
{

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		/**
		 * ABU edit: lien from produit pour compo sur mesure
		 * Ajout des vars : surmesure && surdemande
		 */
		$message = html_entity_decode(Tools::getValue('message'));
		if(Tools::getValue('surmesure') == '1')
			$message = 'Nom – prénom – n° tel (horaires de disponibilité pour le contact par téléphone)

1/ Dimensions de votre pièce ou espace que vous souhaitez aménager (voir fiche aide à la réalisation d’un croquis)
2/ liste des besoins : aménagements (par ex : 1 lit une personne avec dimensions du couchage + 1 bureau + 1 penderie + 3 étagères pour livres)
3/ liste des envies : coloris, poignée, style (par ex : assortiment de coloris bleus et blancs, poignées carrées référence « Cube », lit mezzanine avec bureau sous le lit comme sur la photo du modèle « Ella »)
4/ fourchette de prix envisagée : de 1500€ à 2000€ / de 2000€ à 3000€ etc..
autre : précisez ..';

		if(Tools::getValue('surdemande') == '1')
			$message = 'Nom – prénom – n° tel (horaires de disponibilité pour le contact par téléphone)

Autres meubles de la collection ?';


		$this->context->smarty->assign(array(
			'surmesure'  => Tools::getValue('surmesure') == '1' ? true : false,
			'surdemande' => Tools::getValue('surdemande') == '1' ? true : false,
			'message'    => $message
		));

		parent::initContent();
	}
}