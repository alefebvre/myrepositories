<?php

class SDIS62_Service_Generic
{
    /**
     * @var string
     */
    protected $uri;
    
    /**
     * @var Zend_Rest_Client
     */
    protected $rest_client;

    /**
     * Constructeur
     *
     * @param string $uri Uri de l'API à interroger
     * @param string $api_key Optionnel, Clé pour interroger l'API
     * @return void
     */
    public function __construct($uri, $api_key = null)
    {
        $this->setUri($uri);
    }

    /**
     * Récupération de l'Uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Définition de l'Uri
     *
     * @param string $uri
     * @return SDIS62_Service_Abstract
     * @throws Zend_Service_Exception si l'uri est invalide
     */
    public function setUri($uri)
    {
        if(!Zend_Uri::check($uri))
        {
            throw new Zend_Service_Exception('URL invalide');
        }

        $this->uri = $uri;
        return $this;
    }
    
    /**
     * Définition du client REST
     *
     * @param Zend_Rest_Client $rest_client
     * @return self
     */
    public function setRestClient(Zend_Rest_Client $rest_client)
    {
        $this->rest_client = $rest_client;
        return $this;
    }

    /**
     * Récupération du client REST
     *
     * "Lazy loads" si aucun n'est présent
     *
     * @return Zend_Http_Client
     */
    public function getRestClient()
    {
        if (null === $this->rest_client) {
            $this->setRestClient(new Zend_Rest_Client());
        }
        return $this->rest_client;
    }

    /**
     * Envoi d'une requête
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function request($method, array $args = array())
    {
        $client = new Zend_Rest_Client();
        $client->setUri($this->getUri());
        call_user_func_array(array($client, $method), $args);
        
        return Zend_Json::Decode($client->get());
    }
    
    /**
     * Surcharge de la fonction __call
     * Permet d'appeler une fonction directement.
     * request("method", array(5, 6)) = method(5, 6)
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->request($method, $args);
    }
}