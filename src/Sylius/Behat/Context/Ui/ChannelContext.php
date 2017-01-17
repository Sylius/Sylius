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
use Sylius\Behat\Page\Admin\Channel\CreatePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

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
     * @var CreatePageInterface
     */
    private $channelCreatePage;

    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ChannelContextSetterInterface $channelContextSetter
     * @param ChannelRepositoryInterface $channelRepository
     * @param CreatePageInterface $channelCreatePage
     * @param HomePageInterface $homePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ChannelContextSetterInterface $channelContextSetter,
        ChannelRepositoryInterface $channelRepository,
        CreatePageInterface $channelCreatePage,
        HomePageInterface $homePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelContextSetter = $channelContextSetter;
        $this->channelRepository = $channelRepository;
        $this->channelCreatePage = $channelCreatePage;
        $this->homePage = $homePage;
    }

    /**
     * @Given /^I changed (?:|back )my current (channel to "([^"]+)")$/
     * @When /^I change (?:|back )my current (channel to "([^"]+)")$/
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
        $this->channelCreatePage->nameIt($channelName);
        $this->channelCreatePage->specifyCode($channelName);
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
