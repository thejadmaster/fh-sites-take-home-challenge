<?php

namespace Poker\Utilities;

trait StringUtilities
{
  public function camelCaseToTitleCase(string $input): string
  {
    // Add spaces before each uppercase letter except the first one
    $result = preg_replace('/(?<!^)([A-Z])/', ' $1', $input);

    // Capitalize the first letter of each word
    $result = ucwords($result);

    return $result;
  }
}
