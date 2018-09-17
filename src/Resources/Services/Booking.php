<?php

namespace Visacheck\Visacheck\Resources\Services;


use Visacheck\Visacheck\Resources\AbstractResource;

class Booking extends AbstractResource
{
    
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Booking';
    }
}