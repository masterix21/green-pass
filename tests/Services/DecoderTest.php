<?php

namespace Masterix21\GreenPass\Tests\Services;

use Masterix21\GreenPass\Entities\DiseaseAgents\Covid19;
use Masterix21\GreenPass\Exceptions\InvalidBase45;
use Masterix21\GreenPass\Exceptions\InvalidCborData;
use Masterix21\GreenPass\Exceptions\InvalidCoseData;
use Masterix21\GreenPass\Exceptions\InvalidQrcode;
use Masterix21\GreenPass\Exceptions\InvalidZlib;
use Masterix21\GreenPass\GreenPass;
use Masterix21\GreenPass\Services\Decoder;
use Masterix21\GreenPass\Tests\TestCase;

class DecoderTest extends TestCase
{
    /** @test */
    public function it_throws_invalid_qrcode(): void
    {
        $this->expectException(InvalidQrcode::class);

        Decoder::qrcode('fake-qrcode');
    }

    /** @test */
    public function it_throws_invalid_base45(): void
    {
        $this->expectException(InvalidBase45::class);

        Decoder::base45('fake-qrcode');
    }

    /** @test */
    public function it_throws_invalid_zlib(): void
    {
        $this->expectException(InvalidZlib::class);

        Decoder::zlib('fake-zlib');
    }

    /** @test */
    public function it_throws_invalid_cbor_data(): void
    {
        $this->expectException(InvalidCborData::class);

        Decoder::cbor('fake-cbor-data');
    }

    /** @test */
    public function it_throws_invalid_cose_data(): void
    {
        $this->expectException(InvalidCoseData::class);

        Decoder::cose('fake-cose-data');
    }

    /** @test */
    public function it_decodes_the_qrcode_to_greenpass(): void
    {
        foreach ($this->greenPasses as $types) {
            foreach ($types as $greenPass) {
                $greenPass = Decoder::qrcode($greenPass);

                $this->assertEquals(GreenPass::class, get_class($greenPass));
                $this->assertNotEmpty($greenPass->holder->surname);
            }
        }
    }

    /** @test */
    public function it_resolve_the_disease_agent(): void
    {
        $greenPass = Decoder::qrcode($this->greenPasses['v'][0]);

        $covid19 = new Covid19();

        $this->assertEquals($greenPass->certificate->diseaseAgent->id(), $covid19->id());
        $this->assertEquals($greenPass->certificate->diseaseAgent->display(), $covid19->display());
        $this->assertEquals($greenPass->certificate->diseaseAgent->active(), $covid19->active());
        $this->assertEquals($greenPass->certificate->diseaseAgent->version(), $covid19->version());
        $this->assertEquals($greenPass->certificate->diseaseAgent->system(), $covid19->system());
    }
}
