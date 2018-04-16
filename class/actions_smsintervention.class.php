<?php
/* SMS Intervention - Send SMS since FICHINTER card
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \file    class/actions_smsintervention.class.php
 * \ingroup smsintervention
 * \brief   ActionsSmsIntervention
 *
 * Display button to send SMS
 */

require_once DOL_DOCUMENT_ROOT . '/core/lib/functions.lib.php';

/**
 * Class ActionsSmsIntervention
 */
class ActionsSmsIntervention
{
    /**
     * @var DoliDB Database handler
     */
    private $db;

    /**
     *  Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $conf,$langs, $user, $db;
        
        $langs->load("smsintervention@smsintervention");
        
        $regexTel = '/^(\+33|0)(6|7)([0-9]{8})/';
        
        $errorSms = NULL;
        
        // Vérifie les droits
        if (!isset($user->rights->SmsIntervention) || !isset($user->rights->SmsIntervention->send) || !$user->rights->SmsIntervention->send) {
            return 0;
        }
        
        // Vérifie les constantes SMS et si la connexion se fait etc...
        $applicationKey = $conf->global->SMSINTERVENTION_APPLICATION_KEY;
        $applicationSecret = $conf->global->SMSINTERVENTION_APPLICATION_SECRET;
        $consumerKey = $conf->global->SMSINTERVENTION_CONSUMER_KEY;
        
        if ($applicationKey == '' || $applicationSecret == '' || $consumerKey == '') {
            $errorSms = 'SMS_CONSTANTE_EMPTY';
        }
        
        // Vérifie si un contenu est défini pour ce statut
        if (is_null($errorSms)) {
            $contentSms = unserialize($conf->global->SMSINTERVENTION_SMS_CONTENT);
            if ($contentSms == false || !is_array($contentSms) || !isset($contentSms[$object->array_options['options_suivi']]) || empty($contentSms[$object->array_options['options_suivi']])) {
                $errorSms = 'SMS_CONTENT_EMPTY';
            }
        }
        
        // Vérifie si le numéro à prévenir est un portable
        if (is_null($errorSms)) {
            if (!preg_match($regexTel, $object->array_options['options_num_a_prevenir'])) {
                // Sinon on recherche dans la fiche du tiers (société)
                require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
                $societe = new Societe($this->db);
                $societe->fetch($object->socid);
                if (!preg_match($regexTel, $societe->phone)) {
                    $errorSms = 'SMS_NO_MOBILE';
                }
            }
        }
        
        // Vérifie qu'un SMS n'a pas déjà été envoyé sur cette intervention avec ce Statut
        if (is_null($errorSms)) {
            require_once dol_buildpath('/smsintervention/class/smsinterventionhistory.class.php');
            $myobject=new Smsinterventionhistory($db);
            
            $filter['t.fk_fichinter'] = $object->id;
            $filter['t.status_fichinter'] = $object->array_options['options_suivi'];
            
            $countSmshistory = $myobject->fetchAll('', '', 0, 0, $filter);
            
            if ($countSmshistory > 0) {
                $errorSms = 'SMS_STILL_SEND';
            }
        }
        
        if (is_null($errorSms)) {
            echo '<div class="inline-block divButAction"><button class="butAction" id="smsSendBtn">'.$langs->trans("SEND_SMS").'</button></div>';
            echo '<div id="dialog-confirm" title="'.$langs->trans("SMS_MODAL_TITLE").'" style="display:none;" data-btnSendTxt="'.$langs->trans("SEND_SMS").'" data-btnCancelTxt="'.$langs->trans("SMS_CANCEL_BTN").'">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0px 12px 20px 0;"></span>'.$langs->trans("SMS_MODAL_DESCRIPTION").'</p>
</div>';
            echo '<script>';
            echo 'var ajaxFile="'.dol_buildpath('/smsintervention/ajax/ajaxSendSms.php', 1).'";';
            echo 'var fichinterId="'.$object->id.'";';
            echo '$.getScript("'. dol_buildpath('/smsintervention/js/smsInterventionBtn.js', 1).'");';
            echo '</script>';
        } else {
            echo '<div class="inline-block divButAction"><a class="butActionRefused" title="'.$langs->trans($errorSms).'">'.$langs->trans("SEND_SMS").'</a></div>';
        }
        
        
        return 0;
    }
}
