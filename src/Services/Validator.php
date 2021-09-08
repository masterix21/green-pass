<?php

namespace Masterix21\GreenPass\Services;

use Carbon\Carbon;
use Exception;
use Masterix21\GreenPass\GreenPass;

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
    public static function qrcodeHasPrefix(string $qrcode): bool
    {
        return str_starts_with($qrcode, 'HC1:');
    }

    /**
     * @experimental
     *
     * @param GreenPass   $greenPass
     * @param string|null $country
     * @param array|null  $rules
     *
     * @return bool
     * @throws Exception
     */
    public static function evaluate(GreenPass $greenPass, ?string $country = null, ?array $rules = null): bool
    {
        if ((is_null($country) || $country === '') && (is_null($rules) || count($rules) === 0)) {
            throw new Exception('evaluate requires a country or an array of rules');
        }

        try {
            CertLogic::evaluteRules($rules, $greenPass->toArray());
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
