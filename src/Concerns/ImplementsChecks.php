<?php

namespace Masterix21\GreenPass\Concerns;

use Masterix21\GreenPass\GreenPass;

/** @mixin GreenPass */
trait ImplementsChecks
{
    public static function isFormallyValid($qrcode): bool
    {
        return str_starts_with($qrcode, 'HC1:');
    }
}
