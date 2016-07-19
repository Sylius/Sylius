<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Provider;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class NewCartProvider implements CartProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @param FactoryInterface $cartFactory
     */
    public function __construct(FactoryInterface $cartFactory)
    {
        $this->cartFactory = $cartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->cartFactory->createNew();
    }
}
