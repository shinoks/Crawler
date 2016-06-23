<?php

    
class defaultController 
{
    function __construct()
    {
        $this->twig = new Twig_Environment( new Twig_Loader_Filesystem("./view"),
            array( "cache" => "./view/cache" ) );
    }
    
    
    public function getContactSite()
    {
        return $this->twig->render("contact.html.twig", array()
            );
    }
   
}