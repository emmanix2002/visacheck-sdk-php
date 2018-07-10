<?php

namespace Visacheck\Visacheck\Resources\Vehicle;


use Visacheck\Visacheck\Resources\AbstractResource;

class Usage extends AbstractResource
{
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'VehicleUsage';
    }
}