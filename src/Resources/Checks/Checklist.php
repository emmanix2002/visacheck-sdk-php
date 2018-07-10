<?php

namespace Visacheck\Visacheck\Resources\Checks;


use Visacheck\Visacheck\Resources\AbstractResource;

class Checklist extends AbstractResource
{
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Checklist';
    }
}