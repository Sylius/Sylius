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

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ReviewCreateListener
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    /**
     * @param GenericEvent $event
     */
    public function ensureReviewHasAuthor(GenericEvent $event)
    {
        if (!($subject = $event->getSubject()) instanceof ReviewInterface) {
            throw new UnexpectedTypeException($subject, ReviewInterface::class);
        }

        if (null !== $subject->getAuthor()) {
            return;
        }

        $subject->setAuthor($this->customerContext->getCustomer());
    }
}
