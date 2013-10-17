<?php

abstract class SDIS62_Model_Mapper_WebService_Abstract implements SDIS62_Model_Mapper_Interface
{
    /**
     * @var SDIS62_Service_Generic
     */
    protected $webservice;

    /**
     * Définition du webservice
     *
     * @param SDIS62_Service_Generic $service
     * @return SDIS62_Model_Mapper_WebService_Abstract Interface fluide
     * @throws Zend_Exception Si l'objet n'est pas une instance de SDIS62_Service_Generic
     */
    public function setWebService($service)
    {
        if (!$service instanceof SDIS62_Service_Generic)
        {
            throw new Zend_Exception("L'objet n'est pas une instance de type SDIS62_Service_Generic");
        }
        
        $this->service = $service;
        
        return $this;
    }
 
    /**
     * Récupération du webservice
     * Si le webservice devant être retourné n'est pas du type SDIS62_Service_Generic, on le transforme automatiquement
     *
     * @return Zend_Db_Table_Abstract|null
     */
    public function getWebService()
    {
        if (is_string($this->webservice))
        {
            $this->setWebService($this->webservice);
        }
        
        return $this->webservice;
    }
    
    /**
     * Récupération, construction, et envoi d'une entité correspondant à l'id donnée
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param int $id
     * @return SDIS62_Model_Proxy_Abstract|null
     */
    abstract public function fetchById($id);
    
    /**
     * Récupération, construction, et envoi d'une entité correspondant aux critères spécifiés
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $offset Optionnel
     * @return SDIS62_Model_Proxy_Abstract
     */
    abstract public function fetchRow($where = null, $order = null, $offset = null);

    /**
     * Récupération, construction, et envoi d'une liste d'entités correspondants aux critères spécifiés
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $count Optionnel
     * @param int $offset Optionnel
     * @return array
     */
    abstract public function fetchAll($where = null, $order = null, $count = null, $offset = null);
    
    /**
     * On persiste les données de l'entité (sauvegarde)
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param SDIS62_Model_Proxy_Abstract $entity Paramètre passé en référence
     */
    abstract public function save(SDIS62_Model_Proxy_Abstract &$entity);
    
    /**
     * Suppression des données d'une entité identifiée
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param int $id
     * @return boolean
     */
    abstract public function delete($id);
    
    /**
     * Construction et/ou hydratation de l'entité liée au mapper
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param array $data
     * @return SDIS62_Model_Proxy_Abstract
     */    
    abstract protected function createEntity(array $data);
}