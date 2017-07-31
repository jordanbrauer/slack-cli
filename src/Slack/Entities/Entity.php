<?php

namespace Slack\Entities;

use Slack\Entities\EntityInterface;

class Entity implements EntityInterface
{
  /**
   *
   * Generate table headers from object properties
   *
   * @method getTableHeaders
   * @return array
   */
  static function getTableHeaders ()
  {
    $properties = get_class_vars(get_called_class());

    $headers = array();
    foreach ($properties as $property => $value):
      array_push($headers, $property);
    endforeach;

    return $headers;
  }
}
