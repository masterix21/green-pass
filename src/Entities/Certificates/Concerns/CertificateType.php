<?php

namespace Masterix21\GreenPass\Entities\Certificates\Concerns;

use Masterix21\GreenPass\Entities\DiseaseAgents\DiseaseAgent;

abstract class CertificateType
{
    /**
     * Unique certificate identifier (UVCI).
     *
     * @var string|null
     */
    public ?string $id;

    /**
     * Disease or agent from which the holder has recovered.
     *
     * @var DiseaseAgent
     */
    public DiseaseAgent $diseaseAgent;

    /**
     * Member State or third country in which the vaccine
     * was administered or the test was carried out.
     *
     * @var string|null
     */
    public ?string $country;

    /**
     * Certificate issuer
     *
     * @var string|null
     */
    public ?string $issuer;
}
