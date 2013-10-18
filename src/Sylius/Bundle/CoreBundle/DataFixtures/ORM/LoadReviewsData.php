<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\CoreBundle\Model\ReviewInterface;
use Sylius\Bundle\ReviewBundle\Model\GuestReviewerInterface;

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

            $product = $this->getReference('Sylius.Product.'.$i);

            for ($j = 0;$j <= 5;$j++) {

                $review = $this->createReview();
                $review->setProduct($product);

                $review->setComment($this->faker->paragraph);
                $review->setTitle($this->faker->sentence);
                $review->setRating($this->faker->randomNumber(1, 5));

                $reviewer = $this->get('sylius.repository.guest_reviewer')->createNew();
                $reviewer->setEmail(($this->faker->username).'@example.com');
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
     * @return ReviewInterface
     */
    private function createReview()
    {
        return $this
            ->getReviewRepository()
            ->createNew()
        ;
    }

    /**
     * @return GuestReviewerInterface
     */
    private function createReviewer()
    {
        return $this
            ->getGuestReviewerRepository()
            ->createNew()
        ;
    }
}
