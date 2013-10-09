<?php

class SDIS62_Controller_Action_OauthConsumer extends Zend_Controller_Action
{
    /**
     * Default Zend_Oauth_Consumer object.
     *
     * @var Zend_Oauth_Consumer
     */
	private $consumer = null;
    
    /**
     * The configuration file.
     *
     * @var string
     */
	protected $config_file = null;
    
    /**
     * Array which represents the redirection URL after obtain an access token. ($action, $controller = null, $module = null, array $params = array())
     *
     * @var array
     */
	protected $stock_access_token_url = array("index", "index");

    /**
     * Initialize object
     *
     * @return void
     */
    public function init()
    {
        if($this->config_file == null)
        {
            $this->config_file = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "secret.ini", APPLICATION_ENV);
        }

        // retrieve the configuration from config_file
        $secret_oauth_config = array(
            'callbackUrl' => $this->config_file->oauth->callback,
            'siteUrl' => $this->config_file->oauth->siteurl,
			'consumerKey' => $this->config_file->oauth->consumerkey, // consumer key
			'consumerSecret' => $this->config_file->oauth->consumersecret // consumer secret
        );

        // Initialize consumer object with config array
		$this->consumer = new Zend_Oauth_Consumer($secret_oauth_config);
    }

    /**
     * Dispatch to retrieve a request_token
     *
     * @return void
     */
    public function indexAction()
    {
        // If there is a identity with an access token
        $auth = Zend_Auth::getInstance();
        
        // If we have an access token, redirect
        if($auth->hasIdentity() && isset($auth->getIdentity()->ACCESS_TOKEN))
        {
            $this->_helper->redirector("index", "index");
        }
        
        // get my session
		$session = new Zend_Session_Namespace();

        // Get request_token
        $token = $this->consumer->getRequestToken();

        // we store the request token
        $session->REQUEST_TOKEN = $token;

        // redirect the user to user module
        $this->consumer->redirect();
        
        return;
    }

    /**
     * Dispatch to retrieve an access_token
     *
     * @return void
     */
    public function callbackAction()
    {
        // get my session
		$session = new Zend_Session_Namespace();
        
        // Test if the request token matches with the request token received in step 1
        if($this->getParam("oauth_token") == $session->REQUEST_TOKEN->oauth_token)
        {
            // Get access token
            $token = $this->consumer->getAccessToken(
                $_GET,
                $session->REQUEST_TOKEN
            );
            
            // Serialize and stock access token in session
            $session->ACCESS_TOKEN = serialize($token);

            // Now that we have an Access Token, we can discard the Request Token
            $session->REQUEST_TOKEN = null;
            
            // redirection
            $this->_helper->redirector->gotoSimple(
               $this->stock_access_token_url[0],
                array_key_exists(1, $this->stock_access_token_url) ? $this->stock_access_token_url[1] : null,
                array_key_exists(2, $this->stock_access_token_url) ? $this->stock_access_token_url[2] : null,
                array_key_exists(3, $this->stock_access_token_url) && is_array($this->stock_access_token_url[3]) ? $this->stock_access_token_url[3] : array()
           );
        }
        else
        {
            throw new Exception("Bad Token.", 500);
        }
    }
}