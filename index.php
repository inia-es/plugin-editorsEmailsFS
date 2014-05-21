<?php

/**
 * @defgroup plugins_generic_editorEmailsFS
 */
 
/**
 * @file plugins/generic/editorEmailsFS/index.php
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * A contribution from:
 *	- 2014 Instituto Nacional de Investigacion y Tecnologia Agraria y Alimentaria
 *
 * @ingroup plugins_generic_editorEmailsFS
 * @brief Integrate OJS editorsEmais, insert in editordecisionEmail email to editors in FS.
 *
 */

// $Id$


require_once('editorsEmailsFSPlugin.inc.php');

return new editorsEmailsFSPlugin();

?>
