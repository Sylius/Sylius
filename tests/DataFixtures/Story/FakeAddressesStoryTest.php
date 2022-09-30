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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultChannelsStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeAddressesStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeProductReviewsStoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class FakeAddressesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_fake_addresses(): void
    {
        /** @var FakeAddressesStoryInterface $story */
        $story = self::getContainer()->get('sylius.data_fixtures.story.fake_addresses');

        /** @var RepositoryInterface $addressRepository */
        $addressRepository = self::getContainer()->get('sylius.repository.address');

        $story->build();

        $this->assertCount(11, $addressRepository->findAll());
    }
}
