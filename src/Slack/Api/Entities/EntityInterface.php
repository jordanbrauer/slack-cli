<?php

/**
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */

declare (strict_types = 1);

namespace Slack\Api\Entities;

use Symfony\Component\Console\{
  Output\OutputInterface,
  Style\SymfonyStyle
};

interface EntityInterface
{
  /**
   * Must output a json string
   */
  public static function getJsonOutput (SymfonyStyle $io, $json);

  /**
   * Method for building table headers from the entity
   */
  public static function getTableHeaders ();

  /**
   * Method for building the table rows, 1 per entity
   */
  public static function getTableRows (array $entities = array(), array $parameters = array());

  /**
   * Must output a formatted table string using the getTableHeaders and getTableRows methods
   */
  public static function getTableOutput (OutputInterface $output, array $entities = array(), array $parameters = array());
}
