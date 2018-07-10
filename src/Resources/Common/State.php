<?php

namespace Visacheck\Visacheck\Resources\Common;


use Visacheck\Visacheck\Resources\AbstractResource;

class State extends AbstractResource
{
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'State';
    }
}