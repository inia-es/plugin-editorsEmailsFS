{**
 * settingsForm.tpl
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * A contribution from:
 *	- 2014 Instituto Nacional de Investigacion y Tecnologia Agraria y Alimentaria
 *
 * $Id$
 *}
{strip}
{assign var="pageTitle" value="plugins.generic.editorsEmailsFS.manager.editorsEmailsSettings"}
{include file="common/header.tpl"}
{/strip}
<div id="editorsEmailsSettings">
<div id="description">{translate key="plugins.generic.editorsEmails.manager.settings.description"}</div>

<div class="separator"></div>

<br />

<form method="post" action="{plugin_url path="settings"}">
{include file="common/formErrors.tpl"}

<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="editorsEmailsSiteId" required="true" key="plugins.generic.editorsEmails.manager.settings.editorsEmailsSiteId"}</td>
		<td width="80%" class="value"><input type="text" name="editorsEmailsSiteId" id="editorsEmailsSiteId" value="{$editorsEmailsSiteId|escape}" size="15" maxlength="25" class="textField" />
		<br />
		<span class="instruct">{translate key="plugins.generic.editorsEmails.manager.settings.editorsEmailsSiteIdInstructions"}</span>
	</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="trackingCode-urchin" required="true" key="plugins.generic.editorsEmails.manager.settings.trackingCode"}</td>
		<td width="80%" class="value"><input type="radio" name="trackingCode" id="trackingCode-urchin" value="urchin" {if $trackingCode eq "urchin" || $trackingCode eq ""}checked="checked" {/if}/> {translate key="plugins.generic.editorsEmails.manager.settings.urchin"}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;</td>
		<td width="80%" class="value"><input type="radio" name="trackingCode" id="trackingCode-ga" value="ga" {if $trackingCode eq "ga"}checked="checked" {/if}/> {translate key="plugins.generic.editorsEmails.manager.settings.ga"}</td>
	</tr>
</table>

<br/>

<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}"/><input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
{include file="common/footer.tpl"}
