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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultChannelsStoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultChannelsStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_channels(): void
    {
        /** @var DefaultChannelsStoryInterface $defaultChannelsStory */
        $defaultChannelsStory = self::getContainer()->get('sylius.data_fixtures.story.default_channels');

        $defaultChannelsStory->build();

        $channel = $this->getChannelByCode('FASHION_WEB');
        $this->assertNotNull($channel, sprintf('Channel "%s" was not found but it should.', 'FASHION_WEB'));
        $this->assertEquals('Fashion Web Store', $channel->getName());
        $this->assertEquals('en_US', $channel->getLocales()->first()->getCode());
        $this->assertEquals('USD', $channel->getCurrencies()->first()->getCode());
        $this->assertEquals('MENU_CATEGORY', $channel->getMenuTaxon()->getCode());
        $this->assertTrue($channel->isEnabled());
        $this->assertEquals('localhost', $channel->getHostname());
        $this->assertNull($channel->getThemeName());
        $this->assertEquals('+41 123 456 789', $channel->getContactPhoneNumber());
        $this->assertEquals('contact@example.com', $channel->getContactEmail());
        $this->assertNotNull($channel->getShopBillingData());
        $this->assertEquals('Sylius', $channel->getShopBillingData()->getCompany());
        $this->assertEquals('0001112222', $channel->getShopBillingData()->getTaxId());
        $this->assertEquals('US', $channel->getShopBillingData()->getCountryCode());
        $this->assertEquals('Test St. 15', $channel->getShopBillingData()->getStreet());
        $this->assertEquals('eCommerce Town', $channel->getShopBillingData()->getCity());
        $this->assertEquals('00 33 22', $channel->getShopBillingData()->getPostcode());
    }

    private function getChannelByCode(string $code): ?ChannelInterface
    {
        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = self::getContainer()->get('sylius.repository.channel');

        return $channelRepository->findOneByCode($code);
    }
}
