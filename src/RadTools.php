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

}
