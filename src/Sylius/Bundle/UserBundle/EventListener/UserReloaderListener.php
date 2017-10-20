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

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class UserReloaderListener
{
    /**
     * @var UserReloaderInterface
     */
    private $userReloader;

    /**
     * @param UserReloaderInterface $userReloader
     */
    public function __construct(UserReloaderInterface $userReloader)
    {
        $this->userReloader = $userReloader;
    }

    /**
     * @param GenericEvent $event
     */
    public function reloadUser(GenericEvent $event): void
    {
        $user = $event->getSubject();

        Assert::isInstanceOf($user, UserInterface::class);

        $this->userReloader->reloadUser($user);
    }
}
