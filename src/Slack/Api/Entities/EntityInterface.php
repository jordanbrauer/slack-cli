<?php

declare (strict_types = 1);

namespace Slack\Api\Entities;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * The interface for a Slack API entity. An entity could be
 * a channel, message, user, or any object with properties.
 *
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */
interface EntityInterface
{
  /**
   * The implementing class must be able to output a JSON string
   *
   * @param SymfonyStyle $io An input/ouput SymfonyStyle object
   * @param string|array $json The JSON data structure to format an print out to the console
   * @return null
   */
  public static function getJsonOutput (SymfonyStyle $io, $json);

  /**
   * The implementing class must be able to output a formatted table string using
   * the getTableHeaders and getTableRows methods
   */
  public static function getTableOutput (OutputInterface $output, array $entities = array(), array $parameters = array());

  /**
   * The implementing class must have a helper method for building table column
   * headers from the entity
   *
   * @return array
   */
  public static function getTableHeaders (): array;

  /**
   * Method for building the table rows, 1 per entity
   *
   * @return array
   */
  public static function getTableRows (array $entities = array(), array $parameters = array()): array;
}
