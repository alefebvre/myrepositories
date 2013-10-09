<?php

abstract class SDIS62_Model_Mapper_DbTable_Abstract implements SDIS62_Model_Mapper_Interface
{
    /**
     * La classe DbTable représentant la table mise en jeu dans ce mapper
     * @var Zend_Db_Table_Abstract|string
     */
    protected $dbTable;
    
    /**
     * Tableau mappant le nom des attributs de l'objet et les champs en base de données
     * Exemple array("attribut de l'entité" => "champs en base")
     * @var array
     */
    protected $map;

    /**
     * Définition de la classe DbTable à utiliser
     *
     * @param Zend_Db_Table_Abstract|string $dbTable
     * @return SDIS62_Model_Mapper_DbTable_Abstract Interface fluide
     * @throws Zend_Exception Si l'objet n'est pas une instance de Zend_Db_Table_Abstract
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }
        
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
        {
            throw new Zend_Exception("L'objet n'est pas une instance de type Zend_Db_Table_Abstract");
        }
        
        $this->dbTable = $dbTable;
        
        return $this;
    }
 
    /**
     * Récupération de la DbTable
     * Si la DbTable devant être retournée n'est pas du type Zend_Db_Table_Abstract, on la transforme automatiquement
     *
     * @return Zend_Db_Table_Abstract|null
     */
    public function getDbTable()
    {
        if (is_string($this->dbTable))
        {
            $this->setDbTable($this->dbTable);
        }
        
        return $this->dbTable;
    }
    
    /**
     * Définition du tableau de mappage des données
     *
     * @param array $map
     * @return SDIS62_Model_Mapper_DbTable_Abstract Interface fluide
     */
    public function setMap($map)
    {
        $this->map = $map;
        
        return $this;
    }
 
    /**
     * Récupération du tableau de mappage des données
     * Si la tableau de mappage est vide, on retourne un tableau vide
     *
     * @return array
     */
    public function getMap()
    {
        if ($this->map === null)
        {
            $this->setMap(array());
        }
        
        return $this->map;
    }
    
    /**
     * Récupération du tableau de mappage des données
     * Si la tableau de mappage est vide, on retourne un tableau vide
     *
     * @param array $data Tableau des données à préparer
     * @param boolean $dataFromDb Optionnel, Booléen : Si les données à préparer viennent de la base de données (et pas de l'entité)
     */
    public function mapData(array $data, $dataFromDb = false)
    {
        // On map les nom des champs entre base et entité
        $map = $dataFromDb ? array_flip($this->getMap()) : $this->getMap();

        foreach($data as $key => $value)
        {
            if(array_key_exists($key, $map))
            {
                $newkey = $map[$key];
                $oldkey = $key;
                
                $data[$newkey] = $data[$oldkey];
                unset($data[$oldkey]);
            }
        }
        
        // On enlève les données non présentes en base et on attribut les données par défaut au tableau
        if(!$dataFromDb)
        {
            $data = array_intersect_key($data, array_flip($this->getDbTable()->info(Zend_Db_Table_Abstract::COLS)));
            
            $cols = $this->getDbTable()->info(Zend_Db_Table_Abstract::METADATA);
            
            foreach($data as $key => $value)
            {
                if($value === null)
                {
                    if(array_key_exists($key, $cols) && $cols[$key]["DEFAULT"] !== null)
                    {
                        switch($cols[$key]["DEFAULT"])
                        {
                            case "CURRENT_TIMESTAMP":
                                $data[$key] = new Zend_Db_Expr('CURRENT_TIMESTAMP');
                                break;
                                
                            default:
                                $data[$key] = $cols[$key]["DEFAULT"];
                        }
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Récupération, construction, et envoi d'une entité correspondant à l'id donnée
     *
     * @param int $id
     * @return SDIS62_Model_Proxy_Abstract|null
     */
    public function fetchById($id)
    {
        $row = $this->getDbTable()->find($id)->current();
        
        if(!$row)
        {
            return null;
        }
        
        $data = $row->toArray();
        
        return $this->createEntity($data);
    }
    
    /**
     * Récupération, construction, et envoi d'une entité correspondant aux critères spécifiés
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $offset Optionnel
     * @return SDIS62_Model_Proxy_Abstract
     */
    public function fetchRow($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        
        if(!$row)
        {
            return null;
        }
        
        $data = $row->toArray();
        
        return $this->createEntity($data);
    }

    /**
     * Récupération, construction, et envoi d'une liste d'entités correspondants aux critères spécifiés
     *
     * @param string|array $where Optionnel
     * @param string|array $order Optionnel
     * @param int $count Optionnel
     * @param int $offset Optionnel
     * @return array
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $entities = array();
        $rows = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        
        if($rows)
        {
            foreach($rows as $row)
            {
                $data = $row->toArray();
                $entities[] = $this->createEntity($data);
            }
        }
        
        return $entities;
    }
    
    /**
     * On persiste les données de l'entité (sauvegarde)
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param SDIS62_Model_Proxy_Abstract $entity Paramètre passé en référence
     */
    abstract public function save(SDIS62_Model_Proxy_Abstract &$entity);
    
    /**
     * Suppression des données d'une entité identifiée
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        try
        {
            $this->getDbTable()->find($id)->current()->delete();
            return true;
        }
        catch(Zend_Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Construction et/ou hydratation de l'entité liée au mapper
     * L'implementation de cette fonction est déléguée aux mappers réels
     *
     * @param array $data
     * @return SDIS62_Model_Proxy_Abstract
     */    
    abstract protected function createEntity(array $data);
}