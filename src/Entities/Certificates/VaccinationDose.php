<?php

namespace Masterix21\GreenPass\Entities\Certificates;

use Carbon\Carbon;
use Masterix21\GreenPass\Entities\Certificates\Concerns\CertificateType;

class VaccinationDose extends CertificateType
{
    /**
     * Type of the vaccine or prophylaxis used.
     *
     * @var string|null
     */
    public ?string $type;

    /**
     * Medicinal product used for this specific dose of vaccination.
     *
     * @var string|null
     */
    public ?string $product;

    /**
     * Vaccine marketing authorization holder or manufacturer
     *
     * @var string|null
     */
    public ?string $manufacturer;

    /**
     * Sequence number (positive integer) of the dose given
     * during this vaccination event.
     *
     * @var int
     */
    public int $doseGiven;

    /**
     * Total number of doses (positive integer) in a complete vaccination
     * series according to the used vaccination protocol.
     *
     * @var int
     */
    public int $totalDoses;

    /**
     * The date when the described dose was received.
     *
     * @var Carbon|null
     */
    public ?Carbon $date;

    public function __construct(protected array $data)
    {
        parent::__construct($data);

        $this->type = $data['v']['vp'] ?? null;
        $this->product = $data['v']['mp'] ?? null;
        $this->manufacturer = $data['v']['ma'] ?? null;
        $this->doseGiven = $data['v']['dn'] ?? 0;
        $this->totalDoses = $data['v']['sd'] ?? 0;
        $this->date = ! empty($data['v']['dt'] ?? null) ? Carbon::parse($data['v']['dt']) : null;
    }
}
