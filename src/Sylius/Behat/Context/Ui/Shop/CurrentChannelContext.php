<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CurrentChannelContext implements Context
{
    /**
     * @var ChannelContextSetterInterface
     */
    private $channelContextSetter;

    /**
     * @param ChannelContextSetterInterface $channelContextSetter
     */
    public function __construct(ChannelContextSetterInterface $channelContextSetter)
    {
        $this->channelContextSetter = $channelContextSetter;
    }

    /**
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (that channel)$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )("[^"]+" channel)$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )(channel "[^"]+")$/
     */
    public function iBrowseChannel($channel)
    {
        $this->channelContextSetter->setChannel($channel);
    }
}
