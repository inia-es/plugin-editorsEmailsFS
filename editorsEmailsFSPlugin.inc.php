<?php

/**
 * @file editorsEmailsFSPlugin.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class editorsEmailsPlugin
 * @ingroup plugins_generic_editorsEmails
 *
 * @brief Google Analytics plugin class
 */

// $Id: editorsEmailsPlugin.inc.php,v 1.13 2009/09/22 21:20:37 asmecher Exp $


import('classes.plugins.GenericPlugin');

class editorsEmailsFSPlugin extends GenericPlugin {

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
			HookRegistry::register('Templates::submission::comment::editorDecisionEmail', array($this, 'inserteditorsEmails'));

			
   
			
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
		return 'editorsEmailsFSPlugin';
	}

	function getDisplayName() {
		return Locale::translate('plugins.generic.editorsEmailsFS.displayName');
	}

	function getDescription() {
		return Locale::translate('plugins.generic.editorsEmailsFS.description');
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
	 * Insert editorsEmails  page tag to editorDecisionEmail.tpl
	 */  
	function inserteditorsEmails($hookName, $params) {
		
		if ($this->getEnabled()) {
			$smarty =& $params[1];
			$output =& $params[2];
			$templateMgr =& TemplateManager::getManager();
			$articleId = (int) Request::getUserVar('articleId');
			import('classes.submission.editor.EditorSubmissionDAO');
			$editorSubmissionDAO = new EditorSubmissionDAO;
			$editordecisions = $editorSubmissionDAO->getEditorDecisions($articleId);
	  	$decision = array_pop($editordecisions);

			$currentJournal = $templateMgr->get_template_vars('currentJournal');
	
			if (!empty($currentJournal)) {
				$journal =& Request::getJournal();
				$journalId = $journal->getJournalId();

//import('lib.pkp.classes.user.PKPUser');


                              $supportEmail = $journal->getSetting('supportEmail');
                              $supportName = $journal->getSetting('supportName');

                              $bccontact =array();
                              $bccontact [0]["name"] = $supportName;
                              $bccontact [0]["email"] = $supportEmail;
                              $templateMgr->assign('bccontact',$bccontact);
                               $contactEmail = $journal->getSetting('contactEmail');
                              $contactName = $journal->getSetting('contacName');

                              $ccontact =array();
                              $ccontact [0]["name"] = 'Ricardo Alia';
                              $ccontact [0]["email"] = 'alia@inia.es';
                              if($decision[decision] ==1){
                                 $bccontact [1]["name"] ='Diana Barba';
                                 $bccontact [1]["email"] = 'editorial.secretary@inia.es';
                              }
                              $templateMgr->assign('bcc',$bccontact);
                              $templateMgr->assign('cc',$ccontact);                  
		
		
//						$output .= $templateMgr->fetch($this->getTemplatePath() . 'pageEditorsEmails.tpl'); 
		
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
				$message = Locale::translate('plugins.generic.editorsEmails.enabled');
				$returner = false;
				break;
			case 'disable':
				$this->setEnabled(false);
				$message = Locale::translate('plugins.generic.editorsEmails.disabled');
				$returner = false;
				break;
			case 'settings':
				if ($this->getEnabled()) {
					$this->import('editorsEmailsSettingsForm');
					$form = new editorsEmailsSettingsForm($this, $journal->getJournalId());
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
