<?php

namespace Masterix21\GreenPass\Concerns;

use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\StringStream;
use CBOR\Tag\TagObjectManager;
use Masterix21\GreenPass\Entities\GreenPass;
use Masterix21\GreenPass\Exceptions\InvalidCborNormalizedData;
use Masterix21\GreenPass\Exceptions\InvalidQrcode;
use Mhauri\Base45;

/** @mixin GreenPass */
trait ImplementsDecode
{
    /**
     * Decode the qrcode data.
     *
     * @param string $qrcode
     *
     * @return GreenPass|null
     *
     * @throws InvalidQrcode
     * @throws InvalidCborNormalizedData
     * @throws \Exception
     */
    public static function decode(string $qrcode)
    {
        if (! static::isFormallyValid($qrcode)) {
            throw new InvalidQrcode();
        }

        $base45 = new Base45();

        $decodedBase45 = $base45->decode(substr($qrcode, 4));

        $decodedZlib = zlib_decode($decodedBase45);

        $decoder = new Decoder(new TagObjectManager(), new OtherObjectManager());

        $decodedData = $decoder->decode(new StringStream($decodedZlib))
            ->getValue()
            ->getNormalizedData();

        if (count($decodedData) !== 4) {
            throw new InvalidCborNormalizedData();
        }

        $decodedGreenPassData = $decoder->decode(new StringStream($decodedData[2]))
            ->getNormalizedData();

        if (! $decodedGreenPassData || count($decodedGreenPassData) !== 4) {
            throw new InvalidCborNormalizedData();
        }

        return new self($decodedGreenPassData[-260][1]);
    }
}
