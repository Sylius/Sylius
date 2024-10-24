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

use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener as BasePasswordUpdaterListener;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class PasswordUpdaterListener extends BasePasswordUpdaterListener
{
    /**
     * @throws \InvalidArgumentException
     */
    public function customerUpdateEvent(GenericEvent $event): void
    {
        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $user = $customer->getUser();
        if (null !== $user) {
            $this->updatePassword($user);
        }
    }
}
