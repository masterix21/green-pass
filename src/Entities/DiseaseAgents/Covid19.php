<?php

namespace Masterix21\GreenPass\Entities\DiseaseAgents;

/**
 * @url https://github.com/ehn-dcc-development/ehn-dcc-valuesets/blob/main/disease-agent-targeted.json
 */
class Covid19 extends DiseaseAgent
{
    public static function id(): string
    {
        return "840539006";
    }

    public static function display(): string
    {
        return "COVID-19";
    }

    public static function active(): bool
    {
        return true;
    }

    public static function version(): string
    {
        return "http://snomed.info/sct/900000000000207008/version/20210131";
    }

    public static function system(): string
    {
        return "http://snomed.info/sct";
    }
}
