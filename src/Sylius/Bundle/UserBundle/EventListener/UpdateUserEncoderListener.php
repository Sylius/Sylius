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

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Webmozart\Assert\Assert;

final class UpdateUserEncoderListener
{
    public function __construct(
        private ObjectManager $objectManager,
        private string $recommendedEncoderName,
        private string $className,
        private string $interfaceName,
        private string $passwordParameter,
    ) {
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user instanceof $this->className || !$user instanceof $this->interfaceName) {
            return;
        }

        Assert::methodExists($user, 'getEncoderName');
        if ($user->getEncoderName() === $this->recommendedEncoderName) {
            return;
        }

        $request = $event->getRequest();

        $plainPassword = $request->request->get($this->passwordParameter);
        if (null === $plainPassword || '' === $plainPassword) {
            return;
        }

        $user->setEncoderName($this->recommendedEncoderName);
        $user->setPlainPassword((string) $plainPassword);

        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }
}
