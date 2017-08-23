<?php
namespace CasasoftStandards\Service;

use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
  echo $this->casasoftConversion->getLabel('number_of_rooms');
  echo $this->casasoftConversion->getLabel('number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getValue($gateway_response, 'number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getValue($gateway_response, 'number_of_rooms');
  echo $this->casasoftConversion->getRenderedValue($gateway_response, 'number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getRenderedValue($gateway_response, 'number_of_rooms');
  print_r($this->casasoftConversion->getList($gateway_response, 'key-facts'));
  var_dump($this->casasoftConversion->getList($gateway_response, [['number_of_rooms', 'numeric_value'],['is-new', 'feature']]));
*/
class ConversionService {

    public function __construct($translator, $numvalService, $categoryService, $featureService, $utilityService){
        $this->translator = $translator;
        $this->numvalService = $numvalService;
        $this->categoryService = $categoryService;
        $this->featureService = $featureService;
        $this->utilityService = $utilityService;
    }

    public $templates = [
      'key-facts' => [
          ['visualReferenceId', 'special'],
          ['categories', 'special'],
          ['start', 'special'],
          ['number_of_rooms', 'numeric_value'],
          ['number_of_bathrooms', 'numeric_value'],
          ['number_of_apartments','numeric_value'],
          ['number_of_floors','numeric_value'],
          ['year_built','numeric_value'],
          ['year_last_renovated','numeric_value'],
          ['condition','special'],
          ['ceiling_height','numeric_value'],
          ['volume_gva','numeric_value'],
          ['Wärmeerzeugung','special'],
          ['Wärmeverteilung','special'],
          ['granny-flat','category'],
          ['parcelNumbers','special'],
          ['Erschliessung','special'],
          ['Auflagen','Auflagen'],
          ['zoneTypes','special'],
          ['construction_utilization_number','numeric_value'],
          ['hall_height','numeric_value'],
          ['maximal_floor_loading','numeric_value'],
          ['carrying_capacity_crane','numeric_value'],
          ['carrying_capacity_elevator','numeric_value']
      ]
    ];

    public function createService(ServiceLocatorInterface $serviceLocator){
        return $this;
    }

    public function getLabel($key, $context = 'smart'){
      if ($context == 'smart' || $context == 'numeric_value') {
        $numval = $this->numvalService->getItem($key);
        if ($numval) {return $numval->getLabel();}
      }

      if ($context == 'smart' || $context == 'feature') {
        $feature = $this->featureService->getItem($key);
        if ($feature) {return $feature->getLabel();}
      }

      if ($context == 'smart' || $context == 'category') {
        $category = $this->categoryService->getItem($key);
        if ($category) {return $category->getLabel();}
      }

      if ($context == 'smart' || $context == 'utility') {
        $utility = $this->utilityService->getItem($key);
        if ($utility) {return $utility->getLabel();}
      }

      if ($context == 'smart' || $context == 'special') {
        switch ($key) {
          case 'visualReferenceId': return $this->translator->translate('Reference ID'); break;
          case 'categories': return $this->translator->translate('Categories'); break;
          case 'start': return $this->translator->translate('Available from'); break;
          case 'condition': return $this->translator->translate('Condition'); break;
          case 'Wärmeerzeugung': return 'Wärmeerzeugung'; break;
          case 'Wärmeverteilung': return 'Wärmeverteilung'; break;
          case 'parcelNumbers': return 'parcelNumbers'; break;
          case 'Erschliessung': return 'Erschliessung'; break;
          case 'zoneTypes': return 'zoneTypes'; break;
        }
      }

      return $key;
    }

    public function getRenderedValue($offerEntity, $key, $context = 'smart'){
      $value = $this->getValue($offerEntity, $key, $context);
      return $value;
    }

    public function getValue($offerEntity, $key, $context = 'smart'){
      if ($context == 'smart' || $context == 'numeric_value') {
        $numval = $this->numvalService->getItem($key);
        if ($numval) {
          if (isset($offerEntity['_embedded']['property']['_embedded']['numeric_values'])) {
            foreach ($offerEntity['_embedded']['property']['_embedded']['numeric_values'] as $propnumval) {
              if ($propnumval['key'] == $key) {
                $numval->setValue($propnumval['value']);
              }
            }
          }
          return $numval->getValue();
        }
      }

      if ($context == 'smart' || $context == 'feature') {
        $feature = $this->featureService->getItem($key);
        if ($feature) {
          if (isset($offerEntity['_embedded']['property']['_embedded']['features'])) {
            foreach ($offerEntity['_embedded']['property']['_embedded']['features'] as $propfeature) {
              if ($propfeature['key'] == $key) {
                return true;
              }
            }
          }
          return false;
        }
      }

      if ($context == 'smart' || $context == 'special') {
        switch ($key) {
          case 'visualReferenceId':
            if (isset($offerEntity['_embedded']['property']['visual_reference_id'])) {
              return $offerEntity['_embedded']['property']['visual_reference_id'];
            }
            if (isset($offerEntity['_embedded']['property']['id'])) {
              return $offerEntity['_embedded']['property']['id'];
            }
            break;
          case 'categories':
            $categories = array();
            if (isset($offerEntity['_embedded']['property']['_embedded']['property_categories'])) {
                foreach ($offerEntity['_embedded']['property']['_embedded']['property_categories'] as $cat_item) {
                    $categories[] = $this->getLabel($cat_item['category_id'], 'category');
                }
            }
            return str_replace(' ', '-', implode('-', $categories));
            break;
          case 'start':
            if (isset($offerEntity['_embedded']['property']['start'])) {
              return $offerEntity['_embedded']['property']['start'];
            } else {
              return $this->translator->translate('On Request');
            }
            break;
          case 'condition':
            $conditions = array();
            if (isset($offerEntity['_embedded']['property']['_embedded']['features'])) {
                foreach ($offerEntity['_embedded']['property']['_embedded']['features'] as $feature) {
                  if (in_array($feature['key'], [
                    'is-demolition-property',
                    'is-dilapidated',
                    'is-gutted',
                    'is-first-time-occupancy',
                    'is-well-tended',
                    'is-modernized',
                    'is-renovation-indigent',
                    'is-shell-construction',
                    'is-new-construction',
                    'is-partially-renovation-indigent',
                    'is-partially-refurbished',
                    'is-refurbished'
                  ] ) ) {
                      $conditions[] = $this->getLabel($feature['key'], 'feature');
                  }
                }
            }
            return str_replace(' ', '-', implode('-', $conditions));
            break;
          case 'Wärmeerzeugung':
            return '';
            break;
          case 'Wärmeverteilung':
            return '';
            break;
          case 'parcelNumbers':
            if (isset($offerEntity['_embedded']['property']['parcelNumbers'])) {
              return $offerEntity['_embedded']['property']['parcelNumbers'];
            }
            break;
          case 'Erschliessung':
            $features = array();
            if (isset($offerEntity['_embedded']['property']['_embedded']['features'])) {
                foreach ($offerEntity['_embedded']['property']['_embedded']['features'] as $feature) {
                  if (in_array($feature['key'], [
                    'has-water-supply',
                    'has-sewage-supply',
                    'has-power-supply',
                    'has-gas-supply',
                  ] ) ) {
                      $features[] = $this->getLabel($feature['key'], 'feature');
                  }
                }
            }
            if (count($features) == 4) {
              $this->translator->translate('Fully ***');
            } elseif (count($features)) {
              $this->translator->translate('Partialy ***');
            } else {
              $this->translator->translate('NOT ***');
            }
            return '';
            break;
          case 'zoneTypes':
            if (isset($offerEntity['_embedded']['property']['zoneTypes'])) {
              return $offerEntity['_embedded']['property']['zoneTypes'];
            }
            break;
        }
      }


      return null;
    }

    public function getList($offerEntity, $template = 'key-facts', $filtered = false){
      $list = [];

      if (is_string($template)) {
        if (array_key_exists($template, $this->templates)) {
          $template = $this->templates[$template];
        } else {
          return $list;
        }
      }

      if (!is_array($template)) {
        return $list;
      }

      foreach ($template as $field) {
        $field = [
          'key' => $field[0],
          'context' => ($field[1] ? $field[1] : 'smart'),
          'label' => $this->getLabel($field[0], ($field[1] ? $field[1] : 'smart')),
          'value' => $this->getValue($offerEntity, $field[0], ($field[1] ? $field[1] : 'smart')),
          'renderedValue' => $this->getRenderedValue($offerEntity, $field[0], ($field[1] ? $field[1] : 'smart')),
        ];
        if ($filtered && !$field['value']) {

        } else {
            $list[] = $field;
        }

      }

      return $list;
    }


}
