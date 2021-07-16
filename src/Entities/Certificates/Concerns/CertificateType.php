<?php

namespace Masterix21\GreenPass\Entities\Certificates\Concerns;

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
     * @var string|null
     */
    public ?string $diseaseAgentTargeted;

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

    public function __construct(protected array $data)
    {
        $this->id = $data['v']['ci'] ?? null;
        $this->diseaseAgentTargeted = $data['v']['tg'] ?? null;
        $this->country = $data['v']['co'] ?? null;
        $this->issuer = $data['v']['is'] ?? null;
    }
}
