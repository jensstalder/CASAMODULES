<?php
namespace CasasoftMessenger\Service;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class MessengerServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        //$htmlPurifier = $serviceLocator->get('htmlPurifier');
        $htmlPurifier = false;

        $service = new MessengerService($translator, $htmlPurifier);

        $config = $serviceLocator->get('config');
        $r_config = array();
        if (isset($config['casasoft-messenger'])) {
            $r_config = $config['casasoft-messenger'];
        }
        $service->setConfig($r_config);
        
        return $service;
    }
}