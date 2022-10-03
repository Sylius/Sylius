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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductReviewFactory;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProductReviewFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_product_review_with_default_values(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::createOne();

        $this->assertInstanceOf(ReviewInterface::class, $productReview->object());
        $this->assertNotNull($productReview->getTitle());
        $this->assertNotNull($productReview->getRating());
        $this->assertNotNull($productReview->getComment());
        $this->assertNotNull($productReview->getReviewSubject());
        $this->assertNotNull($productReview->getAuthor());
        $this->assertNotNull($productReview->getStatus());
    }

    /** @test */
    function it_creates_product_review_with_given_title(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withTitle('One ring to rule them all')->create();

        $this->assertEquals('One ring to rule them all', $productReview->getTitle());
    }

    /** @test */
    function it_creates_product_review_with_given_rating(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withRating(5)->create();

        $this->assertEquals(5, $productReview->getRating());
    }

    /** @test */
    function it_creates_product_review_with_given_comment(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withComment('You should not pass.')->create();

        $this->assertEquals('You should not pass.', $productReview->getComment());
    }

    /** @test */
    function it_creates_product_review_with_given_author_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $author = CustomerFactory::createOne();
        $productReview = ProductReviewFactory::new()->withAuthor($author)->create();

        $this->assertEquals($author->object(), $productReview->getAuthor());
    }

    /** @test */
    function it_creates_product_review_with_given_author(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $author = CustomerFactory::createOne()->object();
        $productReview = ProductReviewFactory::new()->withAuthor($author)->create();

        $this->assertEquals($author, $productReview->getAuthor());
    }

    /** @test */
    function it_creates_product_review_with_given_author_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withAuthor('jrr.tokien@lotr.com')->create();

        $this->assertEquals('jrr.tokien@lotr.com', $productReview->getAuthor());
    }

    /** @test */
    function it_creates_product_review_with_given_product_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::createOne();
        $productReview = ProductReviewFactory::new()->withProduct($product)->create();

        $this->assertEquals($product->object(), $productReview->getReviewSubject());
    }

    /** @test */
    function it_creates_product_review_with_given_product(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::createOne()->object();
        $productReview = ProductReviewFactory::new()->withProduct($product)->create();

        $this->assertEquals($product, $productReview->getReviewSubject());
    }

    /** @test */
    function it_creates_product_review_with_given_product_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withProduct('Lord_Of_The_Rings_Book')->create();

        $this->assertEquals('Lord_Of_The_Rings_Book', $productReview->getReviewSubject()->getCode());
    }

    /** @test */
    function it_creates_product_review_with_new_status(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withStatus('new')->create();

        $this->assertEquals('new', $productReview->getStatus());
    }

    /** @test */
    function it_creates_product_review_with_rejected_status(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withStatus('rejected')->create();

        $this->assertEquals('rejected', $productReview->getStatus());
    }

    /** @test */
    function it_creates_product_review_with_accepted_status(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productReview = ProductReviewFactory::new()->withStatus('accepted')->create();

        $this->assertEquals('accepted', $productReview->getStatus());
    }
}
