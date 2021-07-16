<?php

namespace Masterix21\GreenPass\Entities\Certificates;

use Carbon\Carbon;
use Masterix21\GreenPass\Entities\Certificates\Concerns\CertificateType;

class RecoveryStatement extends CertificateType
{
    /**
     * The date of the holder's first positive NAAT test result.
     *
     * @var Carbon|null
     */
    public ?Carbon $date;

    /**
     * The first date on which the certificate is considered to be valid.
     *
     * @var Carbon|null
     */
    public ?Carbon $validFrom;

    /**
     * The last date on which the certificate is considered to be valid,
     * assigned by the certificate issuer.
     *
     * @var Carbon|null
     */
    public ?Carbon $validUntil;

    public function __construct(protected array $data)
    {
        parent::__construct($data);

        $this->date = ! empty($data['r']['fr']) ? Carbon::parse($data['r']['fr']) : null;
        $this->validFrom = ! empty($data['r']['df']) ? Carbon::parse($data['r']['df']) : null;
        $this->validUntil = ! empty($data['r']['du']) ? Carbon::parse($data['r']['du']) : null;
    }
}
