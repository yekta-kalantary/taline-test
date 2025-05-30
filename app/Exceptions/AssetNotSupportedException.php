<?php

namespace App\Exceptions;

use InvalidArgumentException;

class AssetNotSupportedException extends InvalidArgumentException
{
    public function __construct(string $asset)
    {
        parent::__construct("Asset “{$asset}” is not supported.");
    }
}
