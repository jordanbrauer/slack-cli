<?php

namespace Slack\Entities;

use Slack\Entities\EntityInterface;
use Symfony\Component\Console\Helper\Table;

class Entity implements EntityInterface
{
  public static function getJsonOutput
  (\Symfony\Component\Console\Style\SymfonyStyle $io, $json)
  {
    return $io->text($json);
  }

  public static function getTableOutput
  (\Symfony\Component\Console\Output\OutputInterface $output, $entities = [], array $parameters = [])
  {
    $table = new Table($output);
    return $table
      ->setHeaders(Self::getTableHeaders())
      ->setRows(Self::getTableRows($entities, $parameters))
      ->render()
      ;
  }

  /**
   *
   * Generate table headers from object properties
   *
   * @method getTableHeaders
   * @return array
   */
  public static function getTableHeaders ()
  {
    $properties = get_class_vars(get_called_class());

    $headers = array();
    foreach ($properties as $property => $value):
      array_push($headers, $property);
    endforeach;

    return $headers;
  }

  /**
   *
   * Generate a table row from object property values
   * NOTE: Implement within each unique Entity (Channel, Message, etc) based on data needs.
   *
   * @method getTableRows
   * @param array $entities An array of Entity objects
   * @return array
   */
  public static function getTableRows
  (array $entities = [], array $parameters = [])
  {
    return array_map(
      function ($entity) use ($parameters) {
        # Get public methods on current Entity
        $class = new \ReflectionClass(get_class($entity));
        $publicMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        # Extract the getters, hassers & issers from the setters and misc.
        # Set each methods arguments to none by default.
        $getterMethods = array();
        $methodParams = array();
        foreach ($publicMethods as $method):
          if ($method->class == $class->name):
            switch ($method->name):
              case substr($method->name, 0, 3) == "get": # getters
              case substr($method->name, 0, 3) == "has": # hassers
              case substr($method->name, 0, 2) == "is": # issers
                array_push($getterMethods, $method);
                $methodParams[$method->name] = array();
                break;
            endswitch;
          endif;
        endforeach;

        # Merge parameters from console input
        $arguments = array_merge($methodParams, $parameters);

        # cleanup ?
        $class = null;

        # Return an array of Entity values via getters
        return array_map(
          function ($method) use ($entity, $arguments) {
            # NOTE: Pass arguments to Entity getter methods here.
            return call_user_func_array([$entity, $method->name], $arguments[$method->name]);
          },
          $getterMethods
        );
      },
      $entities
    );
  }
}
