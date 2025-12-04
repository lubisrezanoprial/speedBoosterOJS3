<?php
/**
 * @file plugins/generic/speedBooster/SpeedBoosterPlugin.inc.php
 *
 * Speed & Performance Booster Plugin
 * Minify HTML, CSS, and JS output for better performance
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class SpeedBoosterPlugin extends GenericPlugin {
    
    /**
     * Register the plugin
     */
    public function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        
        if ($success && $this->getEnabled($mainContextId)) {
            // Hook untuk intercept output HTML
            HookRegistry::register('TemplateManager::display', array($this, 'handleTemplateDisplay'));
        }
        
        return $success;
    }
    
    /**
     * Get plugin display name
     */
    public function getDisplayName() {
        return __('plugins.generic.speedBooster.displayName');
    }
    
    /**
     * Get plugin description
     */
    public function getDescription() {
        return __('plugins.generic.speedBooster.description');
    }
    
    /**
     * Handle template display - minify output
     */
    public function handleTemplateDisplay($hookName, $args) {
        // Safety check: jangan minify di admin area atau saat debugging
        $request = Application::get()->getRequest();
        $requestPath = $request->getRequestPath();
        
        // Skip minification untuk admin pages dan management
        if (strpos($requestPath, '/management/') !== false || 
            strpos($requestPath, '/admin/') !== false ||
            strpos($requestPath, '/$$$call$$$') !== false) {
            return false;
        }
        
        $templateMgr = $args[0];
        $template = $args[1];
        
        try {
            // Get settings
            $context = $request->getContext();
            $contextId = $context ? $context->getId() : 0;
            
            $minifyHtml = $this->getSetting($contextId, 'minifyHtml');
            $minifyCss = $this->getSetting($contextId, 'minifyCss');
            $minifyJs = $this->getSetting($contextId, 'minifyJs');
            
            // Jika semua dinonaktifkan, skip
            if (!$minifyHtml && !$minifyCss && !$minifyJs) {
                return false;
            }
            
            // Fetch template output
            $output = $templateMgr->fetch($template);
            
            // Load minifier class
            require_once($this->getPluginPath() . '/classes/HtmlMinifier.inc.php');
            $minifier = new HtmlMinifier();
            
            // Apply minification
            if ($minifyHtml) {
                $output = $minifier->minifyHtml($output);
            }
            
            if ($minifyCss) {
                $output = $minifier->minifyCss($output);
            }
            
            if ($minifyJs) {
                $output = $minifier->minifyJs($output);
            }
            
            // Output minified content
            echo $output;
            return true;
            
        } catch (Exception $e) {
            // Jika ada error, log dan return false (tampilkan normal)
            error_log('SpeedBooster Plugin Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get plugin management actions
     */
    public function getActions($request, $actionArgs) {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled() ? array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ) : array(),
            parent::getActions($request, $actionArgs)
        );
    }
    
    /**
     * Manage plugin actions
     */
    public function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $contextId = $context ? $context->getId() : 0;
                
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->register_function('plugin_url', array($this, 'smartyPluginUrl'));
                
                $this->import('SpeedBoosterSettingsForm');
                $form = new SpeedBoosterSettingsForm($this, $contextId);
                
                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }
}