<?php

namespace Visacheck\Visacheck\Resources\Vehicle;


use Visacheck\Visacheck\Resources\AbstractResource;

class Vehicle extends AbstractResource
{
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Vehicle';
    }
}