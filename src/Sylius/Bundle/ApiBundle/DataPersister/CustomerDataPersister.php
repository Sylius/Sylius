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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class CustomerDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private PasswordUpdaterInterface $passwordUpdater,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof CustomerInterface;
    }

    /**
     * @param CustomerInterface $data
     * @param array<string, mixed> $context
     */
    public function persist($data, array $context = []): void
    {
        $user = $data->getUser();
        if (null !== $user && null !== $user->getPlainPassword()) {
            $this->passwordUpdater->updatePassword($user);
        }

        $this->decoratedDataPersister->persist($data, $context);
    }

    /** @param array<string, mixed> $context */
    public function remove($data, array $context = []): void
    {
        $this->decoratedDataPersister->remove($data, $context);
    }
}
