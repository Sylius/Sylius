<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\ChannelContextSetterInterface;
use Sylius\Behat\Page\Channel\ChannelCreatePage;
use Sylius\Behat\Page\Shop\HomePage;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ChannelContextSetterInterface
     */
    private $channelContextSetter;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ChannelCreatePage
     */
    private $channelCreatePage;

    /**
     * @var HomePage
     */
    private $homePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ChannelContextSetterInterface $channelContextSetter
     * @param ChannelRepositoryInterface $channelRepository
     * @param ChannelCreatePage $channelCreatePage
     * @param HomePage $homePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ChannelContextSetterInterface $channelContextSetter,
        ChannelRepositoryInterface $channelRepository,
        ChannelCreatePage $channelCreatePage,
        HomePage $homePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelContextSetter = $channelContextSetter;
        $this->channelRepository = $channelRepository;
        $this->channelCreatePage = $channelCreatePage;
        $this->homePage = $homePage;
    }

    /**
     * @When I change my current channel to :channel
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel)
    {
        $this->channelContextSetter->setChannel($channel);
    }

    /**
     * @When I create a new channel :channelName
     */
    public function iCreateNewChannel($channelName)
    {
        $this->channelCreatePage->open();
        $this->channelCreatePage->fillName($channelName);
        $this->channelCreatePage->fillCode($channelName);
        $this->channelCreatePage->create();

        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);
        $this->sharedStorage->set('channel', $channel);
    }

    /**
     * @When /^I visit (this channel)'s homepage$/
     */
    public function iVisitChannelHomepage(ChannelInterface $channel)
    {
        $this->channelContextSetter->setChannel($channel);

        $this->homePage->open();
    }
}
