<?php
/* SMS Intervention - Send SMS since FICHINTER card
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \defgroup smsintervention Send SMS since FICHINTER CARD
 * \file    core/modules/modSmsIntervention.class.php
 * \ingroup smsintervention
 * \brief   ActionsSmsIntervention
 *
 * Send SMS since FICHINTER CARD
 */

include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

/**
 *  Description and activation class for module MyModule
 */
class modSmsIntervention extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
    public function __construct($db) {
        global $langs,$conf;

        $this->db = $db;
        
        $this->numero = 432411;
                
        $this->rights_class = 'SmsIntervention';

        $this->family = "Inovea Conseil";
	$this->special = 0;

        $this->module_position = 0;

        $this->name = "smsintervention";

        // Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
        $this->description = "Module432411Desc";
        $this->editor_name = 'Inovea Conseil';
        $this->editor_url = 'https://www.inovea-conseil.com';

        $this->version = '1.0';

        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        
        $this->picto='inoveaconseil@smsintervention';

        $this->module_parts = array(
            'css' => array('/smsintervention/css/SmsIntervention.css'),
            'hooks' => array(
                'interventioncard',
            )
        );

        $this->dirs = array();

        // Config pages. Put here list of php page, stored into dolitest/admin directory, to use to setup module.
        $this->config_page_url = array("setup.php@smsintervention");

        // Dependencies
        $this->hidden = false;
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->phpmin = array(5,0);
        $this->need_dolibarr_version = array(3,7);
        $this->langfiles = array("smsintervention@smsintervention");
        
        $this->const = array();

        $this->tabs = array();

        if (! isset($conf->smsintervention) || ! isset($conf->smsintervention->enabled)) {
                $conf->smsintervention=new stdClass();
                $conf->smsintervention->enabled=0;
        }
        
        // Dictionaries
        $this->dictionaries=array();
        $this->boxes = array();	

        // Cronjobs
        $this->cronjobs = array();

        // Permissions
        $this->rights = array();
        $r=0;
        $this->rights[$r][0] = 43241101;
        $this->rights[$r][1] = $langs->trans("RightsSMSI");
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'send';
        $r++;

        $this->menu = array();
        $r=0;
        $r=1;
    }

    /**
     * Init function
     *
     * @param      string	$options    Options when enabling module ('', 'noboxes')
     * @return     int             	1 if OK, 0 if KO
     */
    public function init($options='')
    {
        $sql = array();

        $this->_load_tables('/smsintervention/sql/');
        
        //VERIF EXTRAFIELD FICHINTER
        $extrafields = new ExtraFields($this->db);
        $extrafields_fichinter = $extrafields->fetch_name_optionals_label('fichinter');
        $pos = count($extrafields_fichinter);
        
        // N° à prévenir
        if (!isset($extrafields_fichinter['num_a_prevenir'])) {
            $extrafields->addExtraField('num_a_prevenir', 'N° à prévenir', 'phone', $pos++, null, 'fichinter', 0, 0, '', 0, true, '', 0, 0);
        }
        
        // Statut
        if (!isset($extrafields_fichinter['suivi'])) {
            $params = array(
                'options' => array(
                    1 => 'En attente',
                    2 => 'A traiter',
                    3 => 'Traitement en cours',
                    4 => 'A facturer',
                    5 => 'Terminée',
                    6 => 'Annulée',
                )
            );
            $extrafields->addExtraField('suivi', "Suivi d'intervention", 'select', $pos++, null, 'fichinter', 0, 0, '', $params, true, '', 0, 0);
        }
        
        //type
        if (!isset($extrafields_fichinter['type'])) {
            $params = array(
                'options' => array(
                    1 => 'Produit 1',
                    2 => 'Produit 2',
                    3 => 'Service 1',
                    4 => 'Serice 2',
                )
            );
            $extrafields->addExtraField('type', 'Type', 'select', $pos++, null, 'fichinter', 0, 0, '', $params, true, '', 0, 0);
        }
        

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * @param      string	$options    Options when enabling module ('', 'noboxes')
     * @return     int             	1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }

}

