<?php

class SDIS62_Service_Module_Connect extends SDIS62_Service_Module_Generic
{
    /**
     * Constructeur
     *
     * @param  string $uri
     * @param  Zend_Oauth_Token_Access $accessToken
     * @param  array $oauthOptions Optionnel
     */
    public function __construct($uri, Zend_Oauth_Token_Access $accessToken, array $oauthOptions = array())
    {
        parent::construct($uri);
        
        $oauthOptions['siteUrl'] = $uri . "/oauth";
        $oauthOptions['token'] = $accessToken;
        
        $httpClient = $accessToken->getHttpClient($oauthOptions, $uri . "/oauth");
        $httpClient->setHeaders(array('Accept-Charset' => 'ISO-8859-1,utf-8'));
        
        $this->getRestClient()->setHttpClient($getHttpClient);
        $this->getRestClient()->setUri($uri . '/api');
    }
}