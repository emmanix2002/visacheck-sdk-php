<?php

namespace Visacheck\Visacheck\Resources\Insurance;


use Visacheck\Visacheck\Resources\AbstractResource;

class Policy extends AbstractResource
{
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'InsurancePolicy';
    }
}