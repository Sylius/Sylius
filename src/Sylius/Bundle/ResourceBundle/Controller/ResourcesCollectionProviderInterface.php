<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourcesCollectionProviderInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
