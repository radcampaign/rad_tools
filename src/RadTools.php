<?php

namespace Drupal\rad_tools;

use Drupal\Core\Entity\EntityInterface;

/**
 * A collection of utility functions for Rad developers.
 */
class RadTools {

  /**
   * A shortcut for entity queries.
   *
   * @param string $bundle
   *   A string representing the name of a bundle. A node type if
   *   $entity_type is omitted.
   * @param string $entity_type
   *   A string representing the type of entity to be queried. Defaults to
   *   'node'.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query object that can query the given entity type, prepared with a
   *   bundle condition.
   */
  public static function entityQuery(string $bundle, string $entity_type = 'node') {
    return \Drupal::entityQuery($entity_type)->condition('type', $bundle);
  }

  /**
   * Return TRUE if an entity has any values for a field.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check for values.
   * @param string $field_name
   *   The machine name of the field to check for values.
   *
   * @return bool
   *   TRUE if the entity has values for the field, FALSE if the entity lacks
   *   that field, or has no values.
   */
  public static function hasFieldValue(EntityInterface $entity, string $field_name) {
    return $entity->hasField($field_name) && $entity->get($field_name)->count();
  }

  /**
   * Return the first value of a field on an entity.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The entity containing the field value.
   * @param string $field_name
   *   The name of the field to get the value of.
   * @param string|bool $column
   *   The name of the column containing the field's value. Defaults to 'value',
   *   pass FALSE to return the item.
   *
   * @return string|array
   *   The first value of the field for the entity, or if $column is FALSE, the
   *   first item for the field.
   */
  public static function getFirstValue(EntityInterface $entity, string $field_name, string $column = 'value') {
    $value = NULL;
    if (self::hasFieldValue($entity, $field_name)) {
      $value = $entity->get($field_name)->first()->getValue();
      // Set column to FALSE to return the array.
      if ($column) {
        return $value[$column];
      }
    }
    return $value;
  }

  /**
   * A helpful logger function that allows developers to easily send
   * data to the drupal watchdog service. By default, it is a noice and the
   * type is set to 'rad' but that can be changed by padding a seecond
   * value to the logger.
   * @param  mixed $message anything that can be logged.
   * @param  string $customType The type for the ws log that you want to pass to ws.
   * @param  boolean $pretty If we are printing a json_encoded message, do we want it to be pretty.
   * @return void
   */
  public static function log($message, $customType = '', $pretty = true) {
    if(empty($customType))
      $customType = 'rad';

    if ($message === NULL) {
      $message = 'NULL';
      self::runLog('NULL', $customType);
    }

    $output = '';
    if (gettype($message) == 'object') {
      $output = self::getInstance($message);
    }
    else if (gettype($message) !== 'string') {
      if($pretty){
        $output = json_encode($message, JSON_PRETTY_PRINT);
      }
      else {
       $output = json_encode($message);
      }
    }
    else {
      $output = $message;
    }

    if ( (empty($output) || $output == '') && is_array($message) && count($message) > 0) {
      $tmp = [];
      foreach ($message as $key => $value) {
        $tmp[$key] = self::processInstance($value);
      }

      if ($pretty){
        $output = json_encode($tmp, JSON_PRETTY_PRINT);
      }
      else {
        $output = json_encode($tmp);
      }
    }



    self::runLog($output, $customType);
  }

  /**
   * Provides a useful way of outputing instance information for
   * values in associative arrays of mixed values.
   * @param  mixed $value Anything that might we want to derrive info for.
   * @return string - type and info about whats inside.
   */
  public static function processInstance($value) {
    $gInstance = self::getInstance($value);
    // if our instance is an array or string
    // we can go ahead and extract some helpful info
    // and print it out.
    switch($gInstance) {
      case 'array':
         $gInstance .= '[' . count($value) . ']';
         break;
      case 'string':
        $gInstance .= " => '" . $value . "'";
        break;
    }

    return $gInstance;
  }

  private static function runLog(string $message, string $type) {
    \Drupal::logger($type)->notice($message);
  }

  /**
   * Ever wished there was  away to find out what crazy class you are inheriting
   * from the depths of the evil drupal core? Have you ever been stuck wondering
   * why you can't print the values of what you thought was an array and all you
   * got in watch dog was '@' signs? Have you ever been stuck and just not sure
   * what to google? Have you stared deep into the dark blackness of drupal core
   * only to find yourself staring back at you and realizing that you are tired,
   * hungry, alone and on the verge of tears? Well here's a tiny tourch to help
   * brightened your path.
   *
   * @param  object $object
   */
  public static function getInstance($object) {
    if(gettype($object) == 'object')
      return get_class($object);

    return gettype($object);
  }

}
