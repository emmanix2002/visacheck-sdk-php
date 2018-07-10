<?php

namespace Visacheck\Visacheck\Services\Identity;


use Visacheck\Visacheck\Services\AbstractService;

class Profile extends AbstractService
{
    /**
     * @inheritdoc
     */
    public function requiresAuthorization(): bool
    {
        return true;
    }

    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'Profile';
    }
}