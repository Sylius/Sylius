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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DefaultStoreDataInterface
     */
    private $defaultFranceChannelFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultStoreDataInterface $defaultFranceChannelFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultStoreDataInterface $defaultFranceChannelFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->defaultFranceChannelFactory = $defaultFranceChannelFactory;
    }

    /**
     * @Given the store is operating on a single channel
     */
    public function thatStoreIsOperatingOnASingleChannel()
    {
        $defaultData = $this->defaultFranceChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }
}
