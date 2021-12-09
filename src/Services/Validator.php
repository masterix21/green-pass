<?php

namespace Masterix21\GreenPass\Services;

use Carbon\Carbon;

class Validator
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
    public static function qrcodePrefix(string $qrcode): bool
    {
        if (!function_exists('str_starts_with')) {
            return substr( $qrcode, 0, 4 ) === "HC1:";
        }

        return str_starts_with($qrcode, 'HC1:');
    }

    /**
     * Verify if the green pass is within the validity period.
     *
     * @param null $date
     * @return bool
     */
    public function isValid($date = null): bool
    {
        /*
        $date ??= Carbon::now();

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        */

        /**
         * @TODO:
         *      - Pfizer / Moderna
         *      - Astrazeneca
         *      - Others...
         *      - Test
         *      - Recover
         */

        return false;
    }
}
