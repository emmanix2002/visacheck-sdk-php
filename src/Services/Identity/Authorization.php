<?php

namespace Visacheck\Visacheck\Services\Identity;


use Visacheck\Visacheck\Services\AbstractService;

class Authorization extends AbstractService
{
    
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Authorization';
    }
}