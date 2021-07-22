<?php

namespace Masterix21\GreenPass\Services;

use CBOR\MapObject;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\StringStream;
use CBOR\Tag\GenericTag;
use CBOR\Tag\TagObjectManager;
use Masterix21\GreenPass\Exceptions\InvalidBase45;
use Masterix21\GreenPass\Exceptions\InvalidCborData;
use Masterix21\GreenPass\Exceptions\InvalidCoseData;
use Masterix21\GreenPass\Exceptions\InvalidQrcode;
use Masterix21\GreenPass\Exceptions\InvalidZlib;
use Masterix21\GreenPass\GreenPass;
use Mhauri\Base45;

class Decoder
{
    public static function base45($base45): string
    {
        try {
            $decoder = new Base45();

            return $decoder->decode($base45);
        } catch (\Exception $e) {
            throw new InvalidBase45();
        }
    }

    public static function zlib($zlib): string
    {
        try {
            return zlib_decode($zlib);
        } catch (\Exception $e) {
            throw new InvalidZlib();
        }
    }

    public static function cbor($cbor): array
    {
        $decoder = new \CBOR\Decoder(new TagObjectManager(), new OtherObjectManager());

        $result = $decoder->decode(new StringStream($cbor));

        if (! $result
            || ! is_object($result)
            || get_class($result) !== MapObject::class
            || $result->count() !== 4) {
            throw new InvalidCborData();
        }

        return $result->getNormalizedData();
    }

    public static function cose($cose): array
    {
        $decoder = new \CBOR\Decoder(new TagObjectManager(), new OtherObjectManager());

        $result = $decoder->decode(new StringStream($cose));

        if (! $result
            || ! is_object($result)
            || get_class($result) !== GenericTag::class) {
            throw new InvalidCoseData();
        }

        return $result->getValue()->getNormalizedData();
    }

    public static function qrcode(string $qrcode): GreenPass
    {
        if (! Validator::qrcodePrefix($qrcode)) {
            throw new InvalidQrcode();
        }

        $zlib = static::base45(substr($qrcode, 4));
        $cose = static::cose(static::zlib($zlib));
        $cbor = static::cbor($cose[2]);

        return new GreenPass($cbor[-260][1]);
    }
}
