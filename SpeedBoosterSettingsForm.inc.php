<?php
/**
 * @file plugins/generic/speedBooster/SpeedBoosterSettingsForm.inc.php
 *
 * Settings form for Speed Booster Plugin
 */

import('lib.pkp.classes.form.Form');

class SpeedBoosterSettingsForm extends Form {
    
    /** @var int Context ID */
    private $_contextId;
    
    /** @var SpeedBoosterPlugin Plugin */
    private $_plugin;
    
    /**
     * Constructor
     */
    public function __construct($plugin, $contextId) {
        $this->_contextId = $contextId;
        $this->_plugin = $plugin;
        
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        
        // Validation rules
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }
    
    /**
     * Initialize form data
     */
    public function initData() {
        $contextId = $this->_contextId;
        $plugin = $this->_plugin;
        
        $this->setData('minifyHtml', $plugin->getSetting($contextId, 'minifyHtml'));
        $this->setData('minifyCss', $plugin->getSetting($contextId, 'minifyCss'));
        $this->setData('minifyJs', $plugin->getSetting($contextId, 'minifyJs'));
    }
    
    /**
     * Assign form data to template
     */
    public function fetch($request, $template = null, $display = false) {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request, $template, $display);
    }
    
    /**
     * Read user input
     */
    public function readInputData() {
        $this->readUserVars(array('minifyHtml', 'minifyCss', 'minifyJs'));
    }
    
    /**
     * Save settings
     */
    public function execute(...$functionArgs) {
        $plugin = $this->_plugin;
        $contextId = $this->_contextId;
        
        $plugin->updateSetting($contextId, 'minifyHtml', $this->getData('minifyHtml') ? true : false);
        $plugin->updateSetting($contextId, 'minifyCss', $this->getData('minifyCss') ? true : false);
        $plugin->updateSetting($contextId, 'minifyJs', $this->getData('minifyJs') ? true : false);
        
        parent::execute(...$functionArgs);
    }
}