<?php
namespace CasasoftStandards\Service;

use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Doctrine\ORM\Tools\Pagination\Paginator;

class EnergyService {

    public $items = array();

    public function __construct($translator){
        $this->translator = $translator;

        //set default numvals
        $options = $this->getDefaultOptions();
        foreach ($options as $key => $options) {
            $feature = new Energy;
            $feature->populate($options);
            $feature->setKey($key);
            $this->addItem($feature, $key);
        }

    }

    public function createService(ServiceLocatorInterface $serviceLocator){
        return $this;
    }

    public function getDefaultGroupOptions(){

        return [
            'geak_exterior' => [ #special Own Service
                'label' => $this->translator->translate('GEAK exterior', 'casasoft-standards'),
                'options' => [
                    [
                        value => 1,
                        label => 'A'
                    ],
                    [
                        value => 2,
                        label => 'B'
                    ],
                    [
                        value => 3,
                        label => 'C'
                    ],
                    [
                        value => 4,
                        label => 'D'
                    ],
                    [
                        value => 5,
                        label => 'E'
                    ],
                    [
                        value => 6,
                        label => 'F'
                    ],
                    [
                        value => 7,
                        label => 'G'
                    ],
                ]
            ],
            'geak_total' => [ #special Own Service
                'label' => $this->translator->translate('GEAK total', 'casasoft-standards'),
                'options' => [
                    [
                        value => 1,
                        label => 'A'
                    ],
                    [
                        value => 2,
                        label => 'B'
                    ],
                    [
                        value => 3,
                        label => 'C'
                    ],
                    [
                        value => 4,
                        label => 'D'
                    ],
                    [
                        value => 5,
                        label => 'E'
                    ],
                    [
                        value => 6,
                        label => 'F'
                    ],
                    [
                        value => 7,
                        label => 'G'
                    ],
                ]
            ],
            'heat_distribution' => [ #special Own Service
                'label' => $this->translator->translate('Heat distribution', 'casasoft-standards'),
                'options' => [
                    [
                        value => 'electric',
                        label => $this->translator->translate('Electric heating', 'casasoft-standards') ],
                    [
                        value => 'geothermal-probe',
                        label => $this->translator->translate('Geothermal-probe heating', 'casasoft-standards') ],
                    [
                        value => 'district',
                        label => $this->translator->translate('District heating', 'casasoft-standards') ],
                    [
                        value => 'gas',
                        label => $this->translator->translate('Gas heating', 'casasoft-standards') ],
                    [
                        value => 'wood',
                        label => $this->translator->translate('Wood heating', 'casasoft-standards') ],
                    [
                        value => 'air-water-heatpump',
                        label => $this->translator->translate('Air-water-heatpump heating', 'casasoft-standards') ],
                    [
                        value => 'oil',
                        label => $this->translator->translate('Oil heating', 'casasoft-standards') ],
                    [
                        value => 'pellet',
                        label => $this->translator->translate('Pellet heating', 'casasoft-standards') ],
                    [
                        value => 'heatpump',
                        label => $this->translator->translate('Heatpump heating', 'casasoft-standards') ],
                ]
            ],
            'heat_generation' => [ #special Own Service
                'label' => $this->translator->translate('Heat generation', 'casasoft-standards'),
                'options' => [
                    [
                        value => 'floor',
                        label => $this->translator->translate('Floor heating', 'casasoft-standards')
                    ],
                    [
                        value => 'radiators',
                        label => $this->translator->translate('Radiators', 'casasoft-standards')
                    ],
                ],
            ],

        ];
    }

    public function addItem($obj, $key = null) {
        if ($key == null) {
            $this->items[] = $obj;
        } else {
            if (isset($this->items[$key])) {
                throw new KeyHasUseException("Key $key already in use.");
            }
            else {
                $this->items[$key] = $obj;
            }
        }
    }

    public function deleteItem($key) {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
        else {
            throw new \Exception("Invalid key $key.");
        }
    }

    public function getItem($key) {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        else {
          return null;
            throw new \Exception("Invalid key $key.");
        }
    }

    public function getItems(){
        return $this->items;
    }

    public function keys() {
        return array_keys($this->items);
    }

    public function length() {
        return count($this->items);
    }

    public function keyExists($key) {
        return isset($this->items[$key]);
    }

}
