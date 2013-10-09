<?php

class SDIS62_Layout_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout
{
    /**
     * @var Zend_Controller_Request_Abstract
     */
    private $request;

    /**
     * Action sur le postDispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = $this->getLayout();
        $this->request = $request;

        // Récupération du bon layout en fonction du module
        $layout->setLayoutPath(
            ( file_exists($this->getModulePath() . 'layouts' . DIRECTORY_SEPARATOR . 'scripts') !== null ? $this->getModulePath() : APPLICATION_PATH ) .
            DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'scripts'
        );

        parent::postDispatch($request);
    }

    /**
     * Récupération de la racine du module
     *
     * @return string
     */
    private function getModulePath() 
    {
        $module = $this->request->getModuleName();
        
        return $module == "default" ? APPLICATION_PATH : APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module;
    }
}