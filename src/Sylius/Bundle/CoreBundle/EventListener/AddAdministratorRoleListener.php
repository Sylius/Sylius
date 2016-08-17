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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Repository\RoleRepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddAdministratorRoleListener
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \RuntimeException
     */
    public function addAdministrationRole(GenericEvent $event)
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $event->getSubject();

        if (!$adminUser instanceof AdminUserInterface) {
            throw new \RuntimeException(sprintf('Expected %s, got %s', AdminUserInterface::class, get_class($adminUser)));
        }

        /** @var RoleInterface $administratorRole */
        $administratorRole = $this->roleRepository->findOneBy(['code' => 'administrator']);

        if (null !== $administratorRole) {
            $adminUser->addAuthorizationRole($administratorRole);
        }
    }
}
