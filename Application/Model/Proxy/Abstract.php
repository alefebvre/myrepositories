<?php

abstract class SDIS62_Model_Proxy_Abstract implements SDIS62_Model_Interface_Abstract
{
    /**
     * @var SDIS62_Model_Abstract|null
     */
    protected $entity;

    /**
     * Récupération de l'entité
     * Si l'entité n'est pas déjà explicité, on la charge d'après le nom du proxy
     *
     * @return SDIS62_Model_Abstract
     */
	public function getEntity()
	{
        // Si l'entité n'est pas explicitement determinée, on la devine d'après le nom du proxy
		if($this->entity === null)
		{
            $name_of_entity_class = str_replace("Proxy_", "", get_class($this));
			$this->setEntity(new $name_of_entity_class);
		}
        
		return $this->entity;
	}
    
    /**
     * Définition de l'entité utilisée par le proxy en la préparant au 'lazy load'
     *
     * @param SDIS62_Model_Abstract
     * @return SDIS62_Model_Proxy_Abstract Interface fluide
     */
	protected function setEntity(&$entity)
	{
		$this->entity = $entity;
        
        // On récupère les valeurs de l'entité
        $data = $this->getEntity()->extract();
        
        // On enlève les id
        unset($data["id"]);
    
        // On filtre les valeurs nulles avec un alias :undefined:
        array_walk($data, function(&$var) {
            if($var === null)
            {
                $var = ":undefined:";
            }
        });
        
        // On transforme les entités liées en proxy
        array_walk_recursive($data, function(&$item, $key) {
            if($key === "classname")
            {
                $item = str_replace("", "Proxy_", $item);
            }
        });
        
        // On hydrate l'entité avec ces nouvelles valeurs
        $this->getEntity()->hydrate($data);
        
		return $this;
	}
    
    /**
     * Constructeur (et hydratation de l'entité si le $data est fourni)
     *
     * @param array $data Optionnel
     */
    public function __construct($data = array())
    {
        $this->getEntity()->hydrate($data);
    }

    /**
     * Récupération de l'id de l'entité
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->getEntity()->getId();
    }

    /**
     * Définition de l'id de l'entité
     *
     * @param int|string|null
     * @return SDIS62_Model_Proxy_Abstract Interface fluide
     * @throws Zend_Exception Si l'id de l'entité est déjà spécifié (prévention d'une modification de l'identifiant)
     */
    public function setId($id)
    {
        $this->getEntity()->setId($id);
        return $this;
    }
    
    /**
     * Chargement complet forcé de l'entité
     *
     * @return SDIS62_Model_Proxy_Abstract Interface fluide
     */
    public function forceFullLoad()
    {
        $methods = get_class_methods($this);
        
        foreach($methods as $method)
        {
            if(substr($method, 0, 3) === "get")
            {
                $this->$method();
            }
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
        // On charge complètement l'objet
        $this->forceFullLoad();
        
        // On lance l'extract de l'entité
        $data = $this->getEntity()->extract();
        
        // On annule l'alias de la valeur null
        array_walk($data, function(&$var) {
            if($var === ":undefined:")
            {
                $var = null;
            }
        });
        
        // On retourne les datas
        return $data;
    }
    
    /**
     * Extraction de l'entité dans l'état actuel (l'entité étant lié par le proxy, et donc le lazy load, elle n'est peut être pas entièrement chargée)
     *
     * @return array
     */
    public function extractWithoutForceFullLoad()
    {
        // On lance l'extract de l'entité
        $data = $this->getEntity()->extract();
        
        // On annule l'alias de la valeur null
        array_walk($data, function(&$var) {
            if($var === ":undefined:")
            {
                $var = null;
            }
        });
        
        // On retourne les datas
        return $data;
    }
    
    /**
     * Hydratation (remplissage) de l'entité à partir d'un tableau de données
     *
     * @return SDIS62_Model_Proxy_Abstract Interface fluide
     */
    public function hydrate(array $data)
    {
        array_walk_recursive($data, function(&$item, $key) {
            if($key === "classname")
            {
                $item = str_replace("", "Proxy_", $item);
            }
        });
        
        return $this->getEntity()->hydrate($data);
    }
    
    /**
     * Contrôle si une valeur devant être retournée a été préalablement chargée ou pas
     *
     * @param string $method
     * @param array $args Optionnel
     * @return boolean
     */
    public function isNotLoaded($method, array $args = array())
    {
        $value = call_user_func_array(array($this->getEntity(), $method), $args);
        return $value === ':undefined:' && $this->getId() !== null;
    }
}