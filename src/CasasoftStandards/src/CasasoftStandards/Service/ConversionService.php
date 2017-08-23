<?php
namespace CasasoftStandards\Service;

use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
  echo $this->casasoftConversion->setOfferEntity(['array_of_property']);
  echo $this->casasoftConversion->getLabel('number_of_rooms');
  echo $this->casasoftConversion->getLabel('number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getValue('number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getValue('number_of_rooms');
  echo $this->casasoftConversion->getRenderedValue('number_of_rooms', 'numeric_value');
  echo $this->casasoftConversion->getRenderedValue('number_of_rooms');
  print_r($this->casasoftConversion->getList('key-facts'));
  var_dump($this->casasoftConversion->getList([['number_of_rooms', 'numeric_value'],['is-new', 'feature']]));
*/
class ConversionService {

    public $property = null;

    public function __construct($translator, $numvalService, $categoryService, $featureService, $utilityService){
        $this->translator = $translator;
        $this->numvalService = $numvalService;
        $this->categoryService = $categoryService;
        $this->featureService = $featureService;
        $this->utilityService = $utilityService;

        $this->setProperty([]);
    }


    public function setProperty(Array $data){
      $this->property = $data;

      if (isset($data['_embedded']['property'])) {
          $this->property = $data['_embedded']['property'];
      } elseif (isset($data['_embedded']['provider'])) {
        $this->property = $data;
      }

      //ensure
      if (!isset($this->property['features']) || !$this->property['features']) {
        $this->property['features'] = [];
      }
      if (!isset($this->property['numeric_values']) || !$this->property['numeric_values']) {
        $this->property['numeric_values'] = [];
      }

      //simplify
      if ($this->property['_embedded']['numeric_values']) {
        $this->property['numeric_values'] = $this->property['_embedded']['numeric_values'];
        unset($this->property['_embedded']['numeric_values']);
      }
      if ($this->property['_embedded']['features']) {
        $this->property['features'] = [];
        foreach ($this->property['_embedded']['features'] as $embfeature) {
          $this->property['features'][] = $embfeature['key'];
        }
        unset($this->property['_embedded']['features']);
      }
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
      if ($context == 'smart' || $context == 'special') {
        switch ($key) {
          case 'visualReferenceId': return $this->translator->translate('Reference no.', 'casasoft-standards'); break;
          case 'categories': return $this->translator->translate('Categories', 'casasoft-standards'); break;
          case 'start': return $this->translator->translate('Available from', 'casasoft-standards'); break;
          case 'condition': return $this->translator->translate('Condition', 'casasoft-standards'); break;
          case 'Wärmeerzeugung': return 'Wärmeerzeugung'; break;
          case 'Wärmeverteilung': return 'Wärmeverteilung'; break;
          case 'parcelNumbers': return $this->translator->translate('Plot no.', 'casasoft-standards'); break;
          case 'Erschliessung': return 'Erschliessung'; break;
          case 'zoneTypes': return $this->translator->translate('Zone type', 'casasoft-standards'); break;
          case 'key-facts': return $this->translator->translate('Key facts', 'casasoft-standards'); break;
          case 'areas': return $this->translator->translate('Areas', 'casasoft-standards'); break;
          case 'features': return $this->translator->translate('Features', 'casasoft-standards'); break;
        }
      }

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
      return $key;
    }

    public function getRenderedValue($key, $context = 'smart'){
      if ($context == 'smart' || $context == 'numeric_value') {
        $numval = $this->numvalService->getItem($key);
        if ($numval) {
          if (isset($this->property['numeric_values'])) {
            foreach ($this->property['numeric_values'] as $propnumval) {
              if ($propnumval['key'] == $key) {
                $numval->setValue($propnumval['value']);
              }
            }
          }
          return $numval->getRenderedValue();
        }
      }
      $value = $this->getValue($key, $context);
      return $value;
    }

    public function getValue($key, $context = 'smart'){
      if ($context == 'smart' || $context == 'numeric_value') {
        $numval = $this->numvalService->getItem($key);
        if ($numval) {
          if (isset($this->property['numeric_values'])) {
            foreach ($this->property['numeric_values'] as $propnumval) {
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
          return in_array($key, $this->property['features']);
        }
      }

      if ($context == 'smart' || $context == 'special') {
        switch ($key) {
          case 'visualReferenceId':
            if (isset($this->property['visual_reference_id'])) {
              return $this->property['visual_reference_id'];
            }
            if (isset($this->property['id'])) {
              return $this->property['id'];
            }
            break;
          case 'categories':
            $categories = array();
            if (isset($this->property['_embedded']['property_categories'])) {
                foreach ($this->property['_embedded']['property_categories'] as $cat_item) {
                    $categories[] = $this->getLabel($cat_item['category_id'], 'category');
                }
            }
            return str_replace(' ', '-', implode('-', $categories));
            break;
          case 'start':
            if (isset($this->property['start'])) {
              return $this->property['start'];
            } else {
              return $this->translator->translate('On Request', 'casasoft-standards');
            }
            break;
          case 'condition':
            $conditions = array();
            foreach ($this->property['features'] as $featureKey) {
              if (in_array($featureKey, [
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
                  $conditions[] = $this->getLabel($featureKey, 'feature');
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
            if (isset($this->property['parcelNumbers'])) {
              return $this->property['parcelNumbers'];
            }
            break;
          case 'Erschliessung':
            $features = array();
            foreach ($this->property['features'] as $featureKey) {
              if (in_array($featureKey, [
                'has-water-supply',
                'has-sewage-supply',
                'has-power-supply',
                'has-gas-supply',
              ] ) ) {
                  $features[] = $this->getLabel($featureKey, 'feature');
              }
            }
            if (count($features) == 4) {
              $this->translator->translate('Fully ***', 'casasoft-standards');
            } elseif (count($features)) {
              $this->translator->translate('Partialy ***', 'casasoft-standards');
            } else {
              $this->translator->translate('NOT ***', 'casasoft-standards');
            }
            return '';
            break;
          case 'zoneTypes':
            if (isset($this->property['zoneTypes'])) {
              return $this->property['zoneTypes'];
            }
            break;
        }
      }


      return null;
    }

    public function getList($templateMixed = 'key-facts', $filtered = false){
      $list = [];
      $template = [];
      if (is_string($templateMixed)) {
        if (array_key_exists($templateMixed, $this->templates)) {
          $template = $this->templates[$templateMixed];
        } elseif ($template === 'areas') {
          $template = [];
          foreach ($this->numvalService->getDefaultOptions() as $key => $options) {
            if(strpos($key, 'area_') !== false) {
              $template[] = [$key, 'numeric_value'];
            }
          }
        } elseif ($templateMixed === 'distances') {
          $template = [];
          foreach ($this->numvalService->getDefaultOptions() as $key => $options) {
            if(strpos($key, 'distance_') !== false) {
              $template[] = [$key, 'numeric_value'];
            }
          }
        } elseif ($templateMixed === 'features') {
          $template = [];
          foreach ($this->featureService->getDefaultOptions() as $key => $options) {
            $template[] = [$key, 'feature'];
          }
        } else {
          return $list;
        }
      } else {
        if (!is_array($template)) {
          return $list;
        } else {
          $template = $templateMixed;
        }
      }



      foreach ($template as $field) {
        $rfield = [
          'key' => $field[0],
          'context' => ($field[1] ? $field[1] : 'smart'),
          'label' => $this->getLabel($field[0], ($field[1] ? $field[1] : 'smart')),
          'value' => $this->getValue($field[0], ($field[1] ? $field[1] : 'smart')),
          'renderedValue' => $this->getRenderedValue($field[0], ($field[1] ? $field[1] : 'smart')),
        ];
        if ($filtered && !$rfield['value']) {

        } else {
            $list[] = $rfield;
        }
      }

      if ($templateMixed == 'features') {
        usort($list, function($a, $b) {
            return strcmp($a["label"], $b["label"]);
        });
      }

      return $list;


    }


}
