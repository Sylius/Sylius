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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When /^I change my current (channel to "([^"]+)")$/
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel): void
    {
        $this->sharedStorage->set('hostname', $channel->getHostname());
    }
}
