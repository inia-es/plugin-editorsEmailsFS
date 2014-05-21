<?php

/**
 * @file EditorsEmailsPlugin.inc.php
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * A contribution from:
 *	- 2014 Instituto Nacional de Investigacion y Tecnologia Agraria y Alimentaria
 *
 * @class EditorsEmailsPlugin
 * @ingroup plugins_generic_EditorsEmails
 *
 * @brief Integrate OJS editorsEmais, insert in editordecisionEmail email to editors in FS.
 */

// $Id: EditorsEmailsPlugin.inc.php,v 1.13 2009/09/22 21:20:37 asmecher Exp $


import('classes.plugins.GenericPlugin');

class EditorsEmailsPlugin extends GenericPlugin {

	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed')) return false;
		$this->addLocaleData();
		if ($success) {
			// Insert in email editor-athor mails editors   
			HookRegistry::register('Templates::submission::comment::editorDecisionEmail', array($this, 'insertEditorsEmails'));

			

			
		}
		return $success;
	}

	/**
	 * Get the name of this plugin. The name must be unique within
	 * its category, and should be suitable for part of a filename
	 * (ie short, no spaces, and no dependencies on cases being unique).
	 * @return String name of plugin
	 */
	function getName() {
		return 'EditorsEmailsPlugin';
	}

	function getDisplayName() {
		return Locale::translate('plugins.generic.EditorsEmails.displayName');
	}

	function getDescription() {
		return Locale::translate('plugins.generic.EditorsEmails.description');
	}

	/**
	 * Extend the {url ...} smarty to support this plugin.
	 */
	function smartyPluginUrl($params, &$smarty) {
		$path = array($this->getCategory(), $this->getName());
		if (is_array($params['path'])) {
			$params['path'] = array_merge($path, $params['path']);
		} elseif (!empty($params['path'])) {
			$params['path'] = array_merge($path, array($params['path']));
		} else {
			$params['path'] = $path;
		}

		if (!empty($params['id'])) {
			$params['path'] = array_merge($params['path'], array($params['id']));
			unset($params['id']);
		}
		return $smarty->smartyUrl($params, $smarty);
	}

	



	/**
	 * Determine whether or not this plugin is enabled.
	 */
	function getEnabled() {
		$journal =& Request::getJournal();
		if (!$journal) return false;
		return $this->getSetting($journal->getJournalId(), 'enabled');
	}

	/**
	 * Set the enabled/disabled state of this plugin
	 */
	function setEnabled($enabled) {
		$journal =& Request::getJournal();
		if ($journal) {
			$this->updateSetting($journal->getJournalId(), 'enabled', $enabled ? true : false);
			return true;
		}
		return false;
	}

	/**
	 * Insert Google Analytics page tag to footer
	 */  
	function insertEditorsEmails($hookName, $params) {
		if ($this->getEnabled()) {
			$smarty =& $params[1];
			$output =& $params[2];
			$templateMgr =& TemplateManager::getManager();
			$currentJournal = $templateMgr->get_template_vars('currentJournal');

			if (!empty($currentJournal)) {
				$journal =& Request::getJournal();
				$journalId = $journal->getJournalId();
				$roleDao = &DAORegistry::getDAO('RoleDAO');
				$editors = $roleDao->getUsersByRoleId)(ROLE_ID_EDITOR,$journalId);
				while (!$editors->eof() {
					$editor = &editors->next();
					$editorAddresses[$editor->getEmail()] = $editor->getFullName();
				}

				
					if ($trackingCode == "ga") {
						$output .= $templateMgr->fetch($this->getTemplatePath() . 'pageTagGa.tpl'); 
					} else {
						$output .= $templateMgr->fetch($this->getTemplatePath() . 'pageTagUrchin.tpl'); 
					}
				}
			}
		}
		return false;
	}

 	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($verb, $args, &$message) {
		$templateMgr =& TemplateManager::getManager();
		$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));
		$journal =& Request::getJournal();
		$returner = true;

		switch ($verb) {
			case 'enable':
				$this->setEnabled(true);
				$message = Locale::translate('plugins.generic.EditorsEmails.enabled');
				$returner = false;
				break;
			case 'disable':
				$this->setEnabled(false);
				$message = Locale::translate('plugins.generic.EditorsEmails.disabled');
				$returner = false;
				break;
			case 'settings':
				if ($this->getEnabled()) {
					$this->import('EditorsEmailsSettingsForm');
					$form = new EditorsEmailsSettingsForm($this, $journal->getJournalId());
					if (Request::getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							Request::redirect(null, 'manager', 'plugin');
						} else {
							$this->setBreadCrumbs(true);
							$form->display();
						}
					} else {
						$this->setBreadCrumbs(true);
						$form->initData();
						$form->display();
					}
				} else {
					Request::redirect(null, 'manager');
				}
				break;
			default:
				Request::redirect(null, 'manager');
		}
		return $returner;
	}
}
?>
