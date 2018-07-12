<?php

namespace Visacheck\Visacheck\Services\Identity;


use Visacheck\Visacheck\Services\AbstractService;

class ForgotPassword extends AbstractService
{
    
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'ForgotPassword';
    }
}