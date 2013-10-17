<?php

class SDIS62_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract  implements Countable
{

    /**
     * Instance de Zend_Controller_Action_Helper_FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $flashMessenger;

    /**
     * Constructeur
     *
     * @return void
     */
    public function __construct()
    {
        $this->flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * Interface fluide
     *
     * @return SDIS62_View_Helper_FlashMessenger
     */
    public function flashMessenger()
    {
        return $this;
    }

    /**
     * Genère de l'html
     *
     * @param string $my_key Optionnel, la clé représente le type des messages à récupérer
     * @param string $template Optionnel, Le template représente un message dans une liste
     * @return string
     */
    public function output($my_key = null, $template = '<li class="alert alert-%s" ><button data-dismiss="alert" class="close">&times;</button><strong class="alert-%s">%s</strong> %s</li>')
    {
        // On récupère les messages
        $array_messages = $this->getMessages();

        // On initialise la chaine de sortie
        $output = '';

        // On stocke les messages
        foreach ($array_messages as $row_message) {

            $key = $row_message["context"];

            if($my_key == null || $key == $my_key ) {

                $output .= sprintf($template, $key, $key, $row_message["title"], $row_message["message"]);
            }
        }

        return $output;
    }

    /**
     * Récupère les messages
     *
     * @return array
     */
    private function getMessages()
    {
        // Messages
        $array_messages = $this->flashMessenger->getMessages();
        
        // Current Messages
        $array_currentMessages = $this->flashMessenger->getCurrentMessages();

        return array_unique( array_merge($array_currentMessages, $array_messages) );
    }

    /**
     * Contrôle de l'existance ou non de messages dans la pile
     *
     * @return int
     */
    public function hasMessages()
    {
        return count($this->flashMessenger) + count($this->flashMessenger->getCurrentMessages());
    }

    /**
     * Implémente l'interface Countable
     *
     * @return int
     */
    public function count()
    {
        return $this->flashMessenger->count();
    }
}