<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ProductReviewFixture;

final class ProductReviewFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function product_reviews_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    #[Test]
    public function product_reviews_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    #[Test]
    public function product_review_title_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['title' => 'CUSTOM']]]], 'custom.*.title');
    }

    #[Test]
    public function product_review_rating_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['rating' => 10]]]], 'custom.*.rating');
    }

    #[Test]
    public function product_review_comment_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['comment' => 'CUSTOM']]]], 'custom.*.comment');
    }

    #[Test]
    public function product_review_author_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['author' => 'test@example.com']]]], 'custom.*.author');
    }

    #[Test]
    public function product_review_product_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['product' => 'MARVEL_T_SHIRT']]]], 'custom.*.product');
    }

    #[Test]
    public function product_review_status_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['status' => 'new']]]], 'custom.*.status');
    }

    protected function getConfiguration(): ProductReviewFixture
    {
        return new ProductReviewFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
