<?php
namespace CasasoftStandards\View\Helper;
 
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class Numval extends AbstractHelper{ 
    public $numvalService = false;
    function __construct($numvalService){
       $this->numvalService = $numvalService;
    }

    public function __invoke($item, $seek = 'label', $value = null, $options = array()){
        if (is_string($item)) {
            try {
                $item = $this->numvalService->getItem($item);
                $item->setValue($value);
            } catch (\Exception $e) {
                return '';
            }
        } elseif (!is_object($item)) {
            return '';
        }

        switch ($seek) {
            case 'label':
                return $item->getLabel();
                break;
            case 'value':
                $val = $item->getValue();
                switch ($item->getSi()) {
                    case 'm': $val = $val . ' m'; break;
                    case 'm2': $val = $val . ' m<sup>2</sup>'; break;
                    case 'm3': $val = $val . ' m<sup>3</sup>'; break;
                }

                return $val;
                break;
            case 'icon':
                return $item->getIcon();
                break;
            case 'si':
                return $item->getSi();
                break;
        }
        return '';
    }
}