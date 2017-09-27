<?php

/**
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */

declare (strict_types = 1);

namespace Slack\Api\Entities;

use Symfony\Component\Console\{
  Output\OutputInterface,
  Helper\Table,
  Style\SymfonyStyle
};

abstract class Entity implements EntityInterface
{
  /**
   * @method getJsonOutput
   *
   * Return the default generic JSON output from the API response
   *
   * @param SymfonyStyle $io An instance of Symfony\Component\Console\Style\SymfonyStyle
   * @param $json The JSON string to be output to the console
   *
   * @return string
   */
  public static function getJsonOutput (SymfonyStyle $io, $json)
  {
    return $io->text($json);
  }

  /**
   * @method getTableOutput
   *
   * Return a formatted table output for a Slack entity
   *
   * @param OutputInterface $output An instance of Symfony\Component\Console\OutputInterface
   * @param array $entities An array entities to have rows costructed for the table output
   * @param array $parameter An array containing a list of command parameters, dictating certain output results
   *
   * @return string
   */
  public static function getTableOutput (OutputInterface $output, array $entities = array(), array $parameters = array())
  {
    $table = new Table($output);
    return $table
      ->setHeaders(Self::getTableHeaders())
      ->setRows(Self::getTableRows($entities, $parameters))
      ->render()
      ;
  }

  /**
   * @method getTableHeaders
   *
   * Generate table headers from object properties
   *
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
   * @method getTableRows
   *
   * Generate a table row from object property values
   * NOTE: Implement within each unique Entity (Channel, Message, etc) based on data needs.
   *
   * @param array $entities An array of Entity objects
   * @param array $parameters An array containing a list of command parameters, dictating certain output results
   *
   * @return array
   */
  public static function getTableRows (array $entities = array(), array $parameters = array())
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
