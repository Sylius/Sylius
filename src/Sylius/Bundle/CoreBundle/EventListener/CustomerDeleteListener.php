<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class CustomerDeleteListener
{
    /**
     * @var EntityRepository
     */
    private $reviewRepository;

    /**
     * @var ObjectManager
     */
    private $reviewManager;

    /**
     * @var AverageRatingCalculatorInterface
     */
    private $averageRatingCalculator;

    /**
     * @param EntityRepository                 $reviewRepository
     * @param ObjectManager                    $reviewManager
     * @param AverageRatingCalculatorInterface $averageRatingCalculator
     */
    public function __construct(EntityRepository $reviewRepository, ObjectManager $reviewManager, AverageRatingCalculatorInterface $averageRatingCalculator)
    {
        $this->reviewRepository = $reviewRepository;
        $this->reviewManager = $reviewManager;
        $this->averageRatingCalculator = $averageRatingCalculator;
    }

    /**
     * @param GenericEvent $event
     */
    public function removeCustomerReviews(GenericEvent $event)
    {
        $author = $event->getSubject();
        if (!$author instanceof CustomerInterface) {
            throw new UnexpectedTypeException($author, 'Sylius\Component\Core\Model\CustomerInterface');
        }

        $reviewSubjectsToRecalculate = array();

        foreach ($this->reviewRepository->findBy(array('author' => $author)) as $review) {
            $reviewSubjectsToRecalculate = $this->removeReviewsAndExtractSubject($review, $reviewSubjectsToRecalculate);
        }
        $this->reviewManager->flush();

        foreach ($reviewSubjectsToRecalculate as $reviewSubject) {
            $reviewSubject->setAverageRating($this->averageRatingCalculator->calculate($reviewSubject));
        }
    }

    /**
     * @param ReviewInterface       $review
     * @param ReviewableInterface[] $reviewSubjectsToRecalculate
     *
     * @return array
     */
    private function removeReviewsAndExtractSubject(ReviewInterface $review, array $reviewSubjectsToRecalculate)
    {
        $reviewSubject = $review->getReviewSubject();

        if (!in_array($reviewSubject, $reviewSubjectsToRecalculate)) {
            $reviewSubjectsToRecalculate[] = $reviewSubject;
        }

        $this->reviewManager->remove($review);

        return $reviewSubjectsToRecalculate;
    }
}
