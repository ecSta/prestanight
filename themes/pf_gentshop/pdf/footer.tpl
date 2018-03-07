<table>
	<tr>
		<td style="font-size:6.5pt; font-style:italic; text-align:center; text-decoration:underline;">LES RÉSERVES ÉVENTUELLES DOIVENT ÊTRE NOTIFIÉES SUR LA LETTRE DE VOITURE OU LA CMR ET CONFIRMÉES PAR LETTRE RECOMMANDÉE AU TRANSPORTEUR DANS LES 72 HEURES</td>
	</tr>
	<tr>
		<td style="text-align:center; font-size:6pt; color:#444">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' pdf='true'}
				<br />
			{/if}
			{if isset($shop_details)}
				{$shop_details|escape:'html':'UTF-8'}<br />
			{/if}
			{if isset($free_text)}
				{$free_text|escape:'html':'UTF-8'}
				{if !empty($shop_phone)}
					ou Tel: {$shop_phone|escape:'html':'UTF-8'}
				{/if}
				<br />
			{/if}
		</td>
	</tr>
</table>

