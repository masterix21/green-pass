<?php

namespace Masterix21\GreenPass\Entities;

use Carbon\Carbon;

class Holder
{
    public ?string $surname = null;
    public ?string $standardisedSurname;

    public ?string $forename;
    public ?string $standardisedForename;

    public ?Carbon $dateOfBirth;

    public function __construct(array $data)
    {
        $this->dateOfBirth = ! empty($data['dob'] ?? null) ? Carbon::parse($data['dob']) : null;

        $this->surname = $data['nam']['fn'] ?? null;
        $this->standardisedSurname = $data['nam']['fnt'] ?? null;
        $this->forename = $data['nam']['gn'] ?? null;
        $this->standardisedForename = $data['nam']['gnt'] ?? null;
    }
}
