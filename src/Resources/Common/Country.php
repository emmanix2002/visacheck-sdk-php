<?php

namespace Visacheck\Visacheck\Resources\Common;



use Visacheck\Visacheck\Resources\AbstractResource;

class Country extends AbstractResource
{

    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Country';
    }
}