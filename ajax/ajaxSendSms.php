<?php
/* SMS Intervention - Send SMS since FICHINTER card
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \file    ajax/ajaxSendSms.php
 * \ingroup smsintervention
 * \brief   smsintervention send SMS
 *
 * SEND SMS (POST FICHINTER ID)
 */

// Load Dolibarr environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once dol_buildpath('/smsintervention/lib/sms.php');

global $langs, $user;

// Vérifie les droits
if (!isset($user->rights->SmsIntervention) || !isset($user->rights->SmsIntervention->send) || !$user->rights->SmsIntervention->send) {
    exit;
}

$langs->load("smsintervention@smsintervention");

require_once DOL_DOCUMENT_ROOT . '/fichinter/class/fichinter.class.php';
$fichinter = new Fichinter($db);
$fichinter->fetch(GETPOST('id','alpha'));

        
$regexTel = '/^(\+33|0)(6|7)([0-9]{8})/';

$errorSms = NULL;

// Vérifie les constantes SMS et si la connexion se fait etc...
$applicationKey = $conf->global->SMSINTERVENTION_APPLICATION_KEY;
$applicationSecret = $conf->global->SMSINTERVENTION_APPLICATION_SECRET;
$consumerKey = $conf->global->SMSINTERVENTION_CONSUMER_KEY;

if ($applicationKey == '' || $applicationSecret == '' || $consumerKey == '') {
    $errorSms = 'SMS_CONSTANTE_EMPTY';
}

// Récupère le contenu du SMS
$smsText = '';
if (is_null($errorSms)) {
    $contentSms = unserialize($conf->global->SMSINTERVENTION_SMS_CONTENT);
    if ($contentSms == false || !is_array($contentSms) || !isset($contentSms[$fichinter->array_options['options_suivi']]) || empty($contentSms[$fichinter->array_options['options_suivi']])) {
        $errorSms = 'SMS_CONTENT_EMPTY';
    } else {
        $smsText = $contentSms[$fichinter->array_options['options_suivi']];
        
        // On remplace le type
        $typeid = $fichinter->array_options['options_type'] != '' ? $fichinter->array_options['options_type'] : -1;
        
        $extrafields = new ExtraFields($db);
        $extrafields_fichinter = $extrafields->fetch_name_optionals_label('fichinter');
        
        $type = $langs->trans("SMS_FICHINTER_TYPE");
        if (isset($extrafields_fichinter['type'])) {
            if (isset($extrafields->attribute_param['type']['options'][$typeid])) {
                $type = $extrafields->attribute_param['type']['options'][$typeid];
            }
        }
        
        $smsText = str_replace('__TYPE__', $type, $smsText);
    }
}

// Récupère le numéro sur lequel on envoie le SMS
$telSms = '';
if (is_null($errorSms)) {
    if (!preg_match($regexTel, $fichinter->array_options['options_num_a_prevenir'])) {
        // Sinon on recherche dans la fiche du tiers (société)
        require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
        $societe = new Societe($db);
        $societe->fetch($fichinter->socid);
        if (!preg_match($regexTel, $societe->phone)) {
            $errorSms = 'SMS_NO_MOBILE';
        } else {
            $telSms = $societe->phone;
        }
    } else {
        $telSms = $fichinter->array_options['options_num_a_prevenir'];
    }
}

// Envoi du SMS
if (is_null($errorSms)) {
    // on formate le numéro au format internationnal
    $telSms = substr($telSms, 0, 1) == '0' ? '+33'.substr($telSms, 1, strlen($telSms)-1) : $telSms;
    try {
        $sms = new sms($applicationKey, $applicationSecret, $consumerKey);
        if (!$sms->sendSMS($smsText, $telSms)) {
            $errorSms = 'SMS_SEND_FAILED';
        }
    } catch (Exception $ex) {
        $errorSms = 'SMS_CONNEXION_FAILED';
    }
}

// Retour
if (is_null($errorSms)) {
    //setEventMessages($langs->trans("SMS_SEND"), null, 'mesgs');
    //Historique
    setEventMessages($langs->trans("SMS_SEND"), null, 'mesgs');
    try {
        require_once dol_buildpath('/smsintervention/class/smsinterventionhistory.class.php');
        $myobject=new Smsinterventionhistory($db);

        $myobject->fk_fichinter = $fichinter->id;
        $myobject->fk_user = $user->id;
        $myobject->status_fichinter = $fichinter->array_options['options_suivi'];
        $myobject->num_envoi = $telSms;
        $myobject->content = $smsText;

        $id=$myobject->create($user);
    } catch (Exception $ex) {
        //setEventMessages($ex->getMessage(), null, 'errors');
    }
    
    
} else {
    setEventMessages($langs->trans($errorSms), null, 'errors');
}