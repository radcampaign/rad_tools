<?php

namespace Drupal\rad_tools\Twig;

use Drupal\rad_tools\RadTools as Tools;

class RadToolsTwigExtension extends \Twig_Extension {
  /**
   * {@inheritdoc}
   * @return string
   */
  public function getName() {
    return 'rad_tools_twig';
  }

  /**
   * we declare the extension functions.
   */
  public function getFunctions() {
    return [
      $this->makeSimpleFunc('print_keys'),
      $this->makeSimpleFunc('get_instance'),
    ];
  }

  private function makeSimpleFunc($funcName, $safe = true) {
    $args = [];
    if($safe) {
      $args['is_safe'] = ['html'];
    }

    return new \Twig_SimpleFunction($funcName,
        [$this, $funcName],
        $args
      );
  }

  public function print_keys($array, $name = '') {

    $html = "<br><div>$name KEYS: </div><br>";
    if (is_array($array)) {
      foreach($array as $key => $value) {
        $gInstance = Tools::getInstance($value);
        switch($gInstance) {
          case 'array':
             $gInstance .= '[' . count($value) . ']';
             break;
          case 'string':
            $gInstance .= " => '" . $value . "'";
            break;
        }

        $html .= '<strong>' . $key . ': </strong>' . $gInstance;
        $html .= '<br>';
      }
    }

    return $html;
  }

  public function get_instance($object) {
    return Tools::getInstance($object);
  }
}
