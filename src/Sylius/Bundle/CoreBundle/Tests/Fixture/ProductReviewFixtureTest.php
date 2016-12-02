<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ProductReviewFixture;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductReviewFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_reviews_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function product_reviews_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_review_title_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['title' => 'CUSTOM']]]], 'custom.*.title');
    }

    /**
     * @test
     */
    public function product_review_rating_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['rating' => 10]]]], 'custom.*.rating');
    }

    /**
     * @test
     */
    public function product_review_comment_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['comment' => 'CUSTOM']]]], 'custom.*.comment');
    }

    /**
     * @test
     */
    public function product_review_author_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['author' => 'test@example.com']]]], 'custom.*.author');
    }

    /**
     * @test
     */
    public function product_review_product_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['product' => 'MARVEL_T_SHIRT']]]], 'custom.*.product');
    }

    /**
     * @test
     */
    public function product_review_status_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['status' => 'new']]]], 'custom.*.status');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductReviewFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
