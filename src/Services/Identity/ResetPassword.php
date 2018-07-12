<?php

namespace Visacheck\Visacheck\Services\Identity;


use Visacheck\Visacheck\Services\AbstractService;

class ResetPassword extends AbstractService
{
    
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'ResetPassword';
    }
}