<?php

namespace Poker\Utilities;

trait StringUtilities
{
  public function camelCaseToTitleCase(string $input): string
  {
    // Add spaces before each uppercase letter and capitalize the first letter
    $result = preg_replace('/([a-z])([A-Z])/', '$1 $2', $input);

    // Capitalize the first letter of each word
    $result = ucwords($result);

    return $result;
  }
}
