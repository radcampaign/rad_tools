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
      $this->makeSimpleFunc('rdebug'),
      $this->makeSimpleFunc('get_instance'),
    ];
  }

  /**
   * A helper function for getFunctions. Builds our Twig_SimleFunction
   * instance for us.
   * @param  string  $funcName The name of the function. Must correllate to public function
   *                           in this class.
   * @param  boolean $safe     Whether or not the output can be sent directly to html.
   * @return Twig_SimpleFunction Out goe sthe simpleFunction instance.
   */
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

  /**
   * A debug helper function for twig templates.
   * Prints out an array and gets useful info about the values for us.
   * @param  array $array An array we want to inspect.
   * @param  string $name  Optional - the name of the variable from twig.
   * @return string        The full output html to print to the screen.
   */
  public function rdebug($array, $name = '') {

    if (is_array($array)) {
      $tmp = [];
      foreach($array as $key => $value) {
        $gInstance = Tools::processInstance($value);
        // in the case of classes, processInstance outputs an array
        $tmp[$key] =  $gInstance;
      }
      $array = $tmp;
    }

    $passThru = json_encode($array);

    $markup = "
      <script type=\"text/javascript\">
      // make sure our window var is not undefined
      if (window._raddebug == undefined) { window._raddebug = []; }

      // append our data to rad debug var
      if(window._raddebug['$name'] !== undefined) {
        window._raddebug['$name'] = [window._raddebug['$name']];
        window._raddebug['$name'].push($passThru);
      }
      else {
        window._raddebug['$name'] = $passThru;
      }
      </script>
    ";

    return $markup;
  }

  /**
   * Prints out what the variable is. Since drupal is inheritance hell, this
   * helps a lot in figuring out what to google.
   * @param  any $object Something to pass along.
   * @return string         The type of instance.
   */
  public function get_instance($object) {
    return Tools::getInstance($object);
  }
}
