<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class NewResourceFactory implements NewResourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory)
    {
        if (null === $method = $requestConfiguration->getFactoryMethod()) {
            return $factory->createNew();
        }

        $arguments = array_values($requestConfiguration->getFactoryArguments());

        return $factory->$method(...$arguments);
    }
}
