<?php

interface SDIS62_Model_Interface_Abstract
{
    /**
     * Récupération de l'id de l'entité
     *
     * @return int|string|null
     */
    public function getId();
    
    /**
     * Définition de l'id de l'entité
     *
     * @param int|string|null
     * @return SDIS62_Model_Abstract|SDIS62_Model_Proxy_Abstract Interface fluide
     */
    public function setId($id);
    
    /**
     * Extraction de l'entité en un tableau de données
     *
     * @return array
     */
    public function extract();
    
    /**
     * Hrdratation (remplissage) de l'entité à partir d'un tableau de données
     *
     * @return SDIS62_Model_Abstract|SDIS62_Model_Proxy_Abstract Interface fluide
     */
    public function hydrate(array $data);
}