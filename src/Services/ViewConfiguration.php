<?php

namespace Visacheck\Visacheck\Services;


class ViewConfiguration extends AbstractService
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
        return 'ViewConfiguration';
    }
}