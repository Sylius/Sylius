<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $defaultFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(FactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->defaultFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createNamed($name)
    {
        $channel = $this->defaultFactory->createNew();
        $channel->setName($name);

        return $channel;
    }
}
