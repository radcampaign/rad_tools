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
    $value = '';
    if ($value = $entity->get($field_name)->first()) {
      $value = $value->getValue();
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
    if ($message === NULL) {
      $message = 'NULL';
    }

    if (gettype($message) !== 'string') {
      if($pretty){
        $message = json_encode($message, JSON_PRETTY_PRINT);
      }
      else {
       $message = json_encode($message);
      }
    }

    if(empty($customType))
      $customType = 'rad';

    \Drupal::logger($customType)->notice($message);
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
