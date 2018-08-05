<?php

namespace Visacheck\Visacheck\Resources\Common;


use Visacheck\Visacheck\Resources\AbstractResource;

class Invite extends AbstractResource
{
    /**
     * @inheritdoc
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
        return 'Invite';
    }
}