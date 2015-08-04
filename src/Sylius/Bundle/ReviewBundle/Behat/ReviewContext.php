<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewContext extends DefaultContext
{
    /**
     * @Given /^there are following reviews:$/
     */
    public function thereAreFollowingReviews(TableNode $table)
    {
        $reviewManager = $this->getEntityManager();

        foreach ($table->getHash() as $reviewHash) {
            $review = $this->createReview($reviewHash);

            $reviewManager->persist($review);
        }

        $reviewManager->flush();
    }

    /**
     * @param array $reviewHash
     *
     * @return ReviewInterface
     */
    private function createReview(array $reviewHash)
    {
        $reviewRepository = $this->getRepository('review');

        $review = $reviewRepository->createNew();

        $review->setTitle($reviewHash['title']);
        $review->setRating((int) $reviewHash['rating']);
        $review->setComment($reviewHash['comment']);

        $product = $this->getRepository('product')->findOneBy(array('name' => $reviewHash['product']));
        $review->setProduct($product);

        $author = $this->getRepository('customer')->findOneBy(array('email' => $reviewHash['author']));
        $review->setAuthor($author);

        return $review;
    }

    /**
     * @Given /^I should see (\d+) review(?:|s) on the reviews list$/
     */
    public function iShouldSeeReviewOnTheList($amount)
    {
        $this->assertSession()->elementsCount('css', '.review', $amount);
    }
}