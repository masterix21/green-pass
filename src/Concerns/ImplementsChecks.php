<?php

namespace Masterix21\GreenPass\Concerns;

use Masterix21\GreenPass\GreenPass;

/** @mixin GreenPass */
trait ImplementsChecks
{
    /**
     * Verify if `$qrcode` is formally valid.
     *
     * WARNING: the check isn't enough to be sure that the Green Pass is valid.
     *
     * @param $qrcode
     *
     * @return bool
     */
    public static function isFormallyValid($qrcode): bool
    {
        return str_starts_with($qrcode, 'HC1:');
    }
}
