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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class ReviewCreateListener
{
    public function __construct(private CustomerContextInterface $customerContext)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function ensureReviewHasAuthor(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        Assert::isInstanceOf($subject, ReviewInterface::class);

        if (null !== $subject->getAuthor()) {
            return;
        }

        $customer = $this->customerContext->getCustomer();

        Assert::isInstanceOf($customer, CustomerInterface::class);

        $subject->setAuthor($customer);
    }
}
