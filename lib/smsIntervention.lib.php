<?php
/* SMS Intervention - Send SMS since FICHINTER card
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \file    lib/smsIntervention.lib.php
 * \ingroup smsintervention
 * \brief   ActionsSmsIntervention
 *
 * Show admin header
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function smsInterventionAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("smsintervention@smsintervention");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/smsintervention/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
        $head[$h][0] = dol_buildpath("/smsintervention/admin/smscontent.php", 1);
	$head[$h][1] = $langs->trans("ContentSMS");
	$head[$h][2] = 'contentsms';
	$h++;
        $head[$h][0] = dol_buildpath("/smsintervention/admin/smsinterventionhistory_list.php", 1);
	$head[$h][1] = $langs->trans("History");
	$head[$h][2] = 'historysms';
	$h++;
        
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'smsintervention');

	return $head;
}