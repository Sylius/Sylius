<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewCreateListener
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var AverageRatingCalculatorInterface
     */
    private $averageRatingCalculator;

    /**
     * @var ObjectManager
     */
    private $productManager;

    /**
     * @param AverageRatingCalculatorInterface $averageRatingCalculator
     * @param CustomerContextInterface         $customerContext
     * @param ObjectManager                    $productManager
     */
    public function __construct(
        AverageRatingCalculatorInterface $averageRatingCalculator,
        CustomerContextInterface $customerContext,
        ObjectManager $productManager
    ) {
        $this->customerContext = $customerContext;
        $this->averageRatingCalculator = $averageRatingCalculator;
        $this->productManager = $productManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function controlReviewAuthor(GenericEvent $event)
    {
        if (!($subject = $event->getSubject()) instanceof ReviewInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Review\Model\ReviewInterface');
        }

        if (null !== $subject->getAuthor()) {
            return;
        }

        $subject->setAuthor($this->customerContext->getCustomer());
    }

    /**
     * @param GenericEvent $event
     */
    public function calculateProductAverageRating(GenericEvent $event)
    {
        if (!($subject = $event->getSubject()) instanceof ReviewInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Review\Model\ReviewInterface');
        }

        $reviewSubject = $subject->getProduct();
        $averagePrice = $this->averageRatingCalculator->calculate($reviewSubject);

        $reviewSubject->setAverageRating($averagePrice);
        $this->productManager->flush($reviewSubject);
    }
}
