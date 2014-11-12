<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ReviewInterface;
use Sylius\Component\Review\Model\GuestReviewerInterface;

/**
 * Default reviews
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class LoadReviewsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 120; $i++) {
            /** @var $product ProductInterface */
            $product = $this->getReference('Sylius.Product-'.$i);

            for ($j = 0; $j <= 5; $j++) {
                $review = $this->createReview($product);

                /** @var $reviewer GuestReviewerInterface */
                $reviewer = $this->get('sylius.repository.guest_reviewer')->createNew();
                $reviewer->setEmail($this->faker->safeEmail);
                $reviewer->setName($this->faker->name);
                $review->setGuestReviewer($reviewer);

                $manager->persist($review);
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 7;
    }

    /**
     * Create new review instance.
     *
     * @param ProductInterface $product
     *
     * @return ReviewInterface
     */
    private function createReview(ProductInterface $product)
    {
        /** @var $review ReviewInterface */
        $review = $this->getReviewRepository()->createNew();
        $review->setComment($this->faker->paragraph);
        $review->setTitle($this->faker->sentence);
        $review->setRating($this->faker->randomNumber(1, 5));
        $review->setProduct($product);

        return $review;
    }
}
