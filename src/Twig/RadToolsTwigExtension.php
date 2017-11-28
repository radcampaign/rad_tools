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
    $id = rand();
    $html = '';

    $html .= "
      <style type=\"text/css\">
      #rdebug-$id {
        position: fixed;
        top: 5px;
        left: 5px;
        border: 1px solid black;
        border-radius: 5%;
        background-color: white;
        padding: 15px;
        z-index: 9999;
      }
      #rdebug-$id .rdebug-title {
        font-weight: bold;
        cursor: pointer;
        margin: 10px 0;
        text-decoration: underline;
      }
      #rdebug-$id button {
        margin: 10px 0;
      }
      #rdebug-$id .rdebug-values {
        max-height: 350px;
        max-width: 450px;
        overflow: auto;
      }
      </style>
      <script type=\"text/javascript\">
        function RDebugHideThis$id(id) {
          var elem = document.getElementById(id),
              children = elem.childNodes;
          for(var i = 0; i < children.length; i++) {
            if(children[i].className == 'rdebug-values') {
              children[i].style.display = children[i].style.display == 'none' ? 'block' : 'none';
            }
          }
        }

        function RDebugCloseThis$id(id) {
          document.getElementById(id).remove();
        }
      </script>
    ";
    $html .= '<div class="rdebug-wrapper" id="rdebug-' . $id . '">'; // start wrapper

    $html .= "<div class=\"rdebug-title\" onclick=\"RDebugHideThis$id('rdebug-$id')\">$name Keys & Values</div>";
    $html .= '<div class="rdebug-values">';
    if (is_array($array)) {
      foreach($array as $key => $value) {

        $gInstance = Tools::processInstance($value);
        // in the case of classes, processInstance outputs an array
        if(is_array($gInstance)) {
          $gInstance = json_encode($gInstance, JSON_PRETTY_PRINT);
        }

        $html .= '<strong>' . $key . ': </strong>' . $gInstance;
        $html .= '<br>';
      }
    }
    else {
      $html .= json_encode($array);
    }
    $html .= '</div>';
    $html .= '<div><button onclick="RDebugCloseThis' . $id . '(\'rdebug-' . $id . '\')">Remove</button></div>';
    $html .= '</div>'; // end class rdebug-wrapper

    return $html;
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
