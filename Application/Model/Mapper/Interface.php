<?php

interface SDIS62_Model_Mapper_Interface
{
    /**
     * Récupération, construction, et envoi d'une entité correspondant à l'id donnée
     *
     * @param int $id
     * @return SDIS62_Model_Proxy_Abstract|null
     */
    public function fetchById($id);
    
    /**
     * Récupération, construction, et envoi d'une entité correspondant aux critères spécifiés
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $offset Optionnel
     * @return SDIS62_Model_Proxy_Abstract|null
     */
    public function fetchRow($where = null, $order = null, $offset = null);

    /**
     * Récupération, construction, et envoi d'une liste d'entité correspondants aux critères spécifiés
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $count Optionnel
     * @param int $offset Optionnel
     * @return array<SDIS62_Model_Proxy_Abstract>|null
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null);
    
    /**
     * On persiste les données de l'entité (sauvegarde)
     *
     * @param SDIS62_Model_Proxy_Abstract $entity Paramètre passé en référence
     */
    public function save(SDIS62_Model_Proxy_Abstract &$entity);

    /**
     * Suppression des données d'une entité identifiée
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id);
}