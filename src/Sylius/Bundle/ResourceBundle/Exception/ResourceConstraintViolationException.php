<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Exception;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Majid Golshadi <golshadi.majid@gmail.com>
 */
class ResourceConstraintViolationException extends ForeignKeyConstraintViolationException
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var RequestConfiguration
     */
    protected $requestConfiguration;

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param ResourceInterface $resource
     */
    public function setResource(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return RequestConfiguration
     */
    public function getRequestConfiguration()
    {
        return $this->requestConfiguration;
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     */
    public function setRequestConfiguration(RequestConfiguration $requestConfiguration)
    {
        $this->requestConfiguration = $requestConfiguration;
    }
}
