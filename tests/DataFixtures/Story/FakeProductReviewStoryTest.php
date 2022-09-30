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
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeProductReviewsStoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class FakeProductReviewStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_fake_reviews(): void
    {
        /** @var FakeProductReviewsStoryInterface $story */
        $story = self::getContainer()->get('sylius.data_fixtures.story.fake_product_reviews');

        /** @var RepositoryInterface $productReviewRepository */
        $productReviewRepository = self::getContainer()->get('sylius.repository.product_review');

        LocaleFactory::new()->withCode('en_US')->create();

        $story->build();

        $this->assertCount(40, $productReviewRepository->findAll());
    }
}
