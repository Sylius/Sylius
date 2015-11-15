<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request configuration factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface RequestConfigurationFactoryInterface
{
    /**
     * @param ResourceMetadataInterface $metadata
     * @param Request $request
     *
     * @return RequestConfiguration
     */
    public function create(ResourceMetadataInterface $metadata, Request $request);
}
