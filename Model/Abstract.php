<?php

abstract class SDIS62_Model_Abstract implements SDIS62_Model_Interface_Abstract
{
    /**
     * @var int|string|null $id
     */
    protected $id;
    
    /**
     * Constructeur (et hydratation de l'entité si le $data est fourni)
     *
     * @param array $data Optionnel
     */
    public function __construct($data = array())
    {
        $this->hydrate($data);
    }

    /**
     * Récupération de l'id de l'entité
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Définition de l'id de l'entité
     *
     * @param int|string|null $id
     * @return SDIS62_Model_Abstract Interface fluide
     * @throws Zend_Exception Si l'id de l'entité est déjà spécifié (prévention d'une modification de l'identifiant)
     */
    public function setId($id)
    {
        if($this->getId($id) === null)
        {
            $this->id = $id;
        }
        else
        {
            throw new Zend_Exception("L'id d'une entité ne peut pas être modifié");
        }
        
        return $this;
    }

    /**
     * Extraction de l'entité en un tableau de données
     *
     * @return array
     */
    public function extract()
    {
        $vars = get_object_vars($this);
        $this->extractRecursive($vars);
        $vars["classname"] = get_class($this);
        return $vars;
    }

    /**
     * Fonction permettant l'extraction d'un objet de façon récursive
     *
     * @param array $array Paramètre passé par référence
     */
    protected function extractRecursive(array &$array)
    {
        foreach($array as $key => &$var)
        {
            if(is_object($var))
            {
                if($var instanceof SDIS62_Model_Proxy_Abstract)
                {
                    $var = $var->getEntity()->extract();
                }
                else
                {
                    $var = $var->extract();
                }
            }
            elseif(is_array($var))
            {   
                $this->extractRecursive($var);
            }
        }
    }
    
    /**
     * Hydratation (remplissage) de l'entité à partir d'un tableau de données
     *
     * @return SDIS62_Model_Abstract Interface fluide
     */
    public function hydrate(array $data)
    {
        $this->hydrateRecursive($data);
    
        foreach($data as $n => $v)
		{
            if(array_key_exists($n, $this->extract()))
            {
                $this->$n = $v;
            }
        }

		return $this;
    }
    
    /**
     * Fonction permettant d'hydrater un objet de façon récursive
     *
     * @param array $array Paramètre passé par référence
     */
    private function hydrateRecursive(array &$array) 
    {
        if(is_array($array))
        {
            foreach($array as &$item)
            {
                if(is_array($item))
                {
                    $this->hydrateRecursive($item);

                    if(array_key_exists("classname", $item))
                    {
                        $object = new $item["classname"];
                        $object->hydrate($item);
                        $item = $object;
                        unset($object);
                    }
                }
            }
        }
    }
}