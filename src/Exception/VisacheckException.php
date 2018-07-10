<?php

namespace Visacheck\Visacheck\Exception;


class VisacheckException extends \RuntimeException
{
    /** @var array  */
    public $context;
    
    /**
     * VisacheckException constructor.
     *
     * @param string $message
     * @param array  $context
     */
    public function __construct(string $message = "", array $context = [])
    {
        parent::__construct($message, 0, null);
        $this->context = $context ?: [];
    }
}