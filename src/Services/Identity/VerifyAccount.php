<?php

namespace Visacheck\Visacheck\Services\Identity;


use Visacheck\Visacheck\Services\AbstractService;

class VerifyAccount extends AbstractService
{
    /**
     * @return $this
     */
    protected function prefillBody()
    {
        $this->body['client_id'] = $this->sdk->getClientId();
        $this->body['client_secret'] = $this->sdk->getClientSecret();
        return $this;
    }
    
    /**
     * Returns the name of the resource.
     *
     * @return string
     */
    function getName(): string
    {
        return 'VerifyAccount';
    }
}