<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Util;

/**
 * Take a comma-delimited string and convert it to an array,
 * trimming and removing any empty values.
 *
 * @param string|array $value
 *
 * @return array
 */
function comma_str_to_array($value)
{
    if ( ! \is_array($value)) {
        $value = \explode(',', (string) $value);
    }

    return \array_values(\array_filter(
        \array_map('trim', $value)
    ));
}
