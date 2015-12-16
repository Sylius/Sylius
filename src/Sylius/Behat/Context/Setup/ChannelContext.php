<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\DefaultStoreDataInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContext implements Context
{
    /**
     * @var DefaultStoreDataInterface
     */
    private $defaultFranceChannelFactory;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param DefaultStoreDataInterface $defaultFranceChannelFactory
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        DefaultStoreDataInterface $defaultFranceChannelFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->defaultFranceChannelFactory = $defaultFranceChannelFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given that store is operating on the France channel
     */
    public function thatStoreIsOperatingOnTheFranceChannel()
    {
        $defaultData = $this->defaultFranceChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }
}
