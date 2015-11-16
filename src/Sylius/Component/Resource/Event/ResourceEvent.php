<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Event;

use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Resource event.
 *
 * @param Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceEvent extends Event
{
    const TYPE_ERROR   = 'error';
    const TYPE_INFO    = 'info';
    const TYPE_WARNING = 'warning';

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var ResourceMetadataInterface
     */
    protected $metadata;

    /**
     * Response code.
     *
     * @var integer
     */
    protected $responseCode = 500;

    /**
     * @var string
     */
    protected $messageType;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param array
     */
    protected $messageParameters = array();

    /**
     * @param ResourceInterface $resource
     * @param ResourceMetadataInterface $metadata
     */
    public function __construct(ResourceInterface $resource, ResourceMetadataInterface $metadata)
    {
        $this->resource = $resource;
        $this->metadata = $metadata;
    }

    /**
     * @param $message
     * @param string $type
     * @param array $parameters
     * @param int $responseCode
     */
    public function stop($message, $type = self::TYPE_ERROR, $parameters = array(), $responseCode = 500)
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParameters = $parameters;
        $this->responseCode = $responseCode;

        $this->stopPropagation();
    }

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return ResourceMetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }
}
