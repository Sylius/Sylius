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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductReviewFactoryInterface;
use Zenstruck\Foundry\Story;

final class FakeProductReviewsStory extends Story implements FakeProductReviewsStoryInterface
{
    public function __construct(private ProductReviewFactoryInterface $productReviewFactory)
    {
    }

    public function build(): void
    {
        $this->productReviewFactory::createMany(40);
    }
}
