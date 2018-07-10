<?php

namespace Visacheck\Visacheck\Resources\Users;



use Visacheck\Visacheck\Resources\AbstractResource;

class User extends AbstractResource
{

    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'User';
    }
}