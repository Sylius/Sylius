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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class UpdateUserEncoderListener
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var string */
    private $recommendedEncoderName;

    /** @var string */
    private $className;

    /** @var string */
    private $interfaceName;

    /** @var string */
    private $passwordParameter;

    public function __construct(
        ObjectManager $objectManager,
        string $recommendedEncoderName,
        string $className,
        string $interfaceName,
        string $passwordParameter
    ) {
        $this->objectManager = $objectManager;
        $this->recommendedEncoderName = $recommendedEncoderName;
        $this->className = $className;
        $this->interfaceName = $interfaceName;
        $this->passwordParameter = $passwordParameter;
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

        if ($user->getEncoderName() === $this->recommendedEncoderName) {
            return;
        }

        $request = $event->getRequest();

        $plainPassword = $request->request->get($this->passwordParameter);
        if (null === $plainPassword || '' === $plainPassword) {
            return;
        }

        $user->setEncoderName($this->recommendedEncoderName);
        $user->setPlainPassword($plainPassword);

        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }
}
