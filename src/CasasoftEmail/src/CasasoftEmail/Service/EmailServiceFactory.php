<?php
namespace CasasoftEmail\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $viewRenderer = $serviceLocator->get('viewRenderer');
        
      

        $resolver = $serviceLocator->get('Zend\View\Resolver\TemplatePathStack');

        try {
            $casasoftMailTemplate = $serviceLocator->get('CasasoftMailTemplate');
        } catch (\Exception $e) {
            $casasoftMailTemplate = false;
        }
        

        $service = new EmailService($translator, $viewRenderer, $resolver, $casasoftMailTemplate);
        
        $config = $serviceLocator->get('config');
        $r_config = array();
        if (isset($config['casasoft-email'])) {
            $r_config = $config['casasoft-email'];
        }
        $service->setConfig($r_config);

        return $service;
    }
}