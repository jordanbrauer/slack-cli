<?php

namespace Slack\Entities;

interface EntityInterface
{
  public static function getTableHeaders ();

  public static function getTableRows (array $entities = [], array $parameters = []);
}
