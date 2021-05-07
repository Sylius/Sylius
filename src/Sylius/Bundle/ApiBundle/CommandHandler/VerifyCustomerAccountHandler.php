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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use InvalidArgumentException;
use Sylius\Bundle\ApiBundle\Command\VerifyCustomerAccount;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental  */
final class VerifyCustomerAccountHandler implements MessageHandlerInterface
{
    /** @var RepositoryInterface */
    private $shopUserRepository;

    public function __construct(RepositoryInterface $shopUserRepository)
    {
        $this->shopUserRepository = $shopUserRepository;
    }

    public function __invoke(VerifyCustomerAccount $command): JsonResponse
    {
        /** @var UserInterface|null $user */
        $user = $this->shopUserRepository->findOneBy(['emailVerificationToken' => $command->token]);
        if (null === $user) {
            throw new InvalidArgumentException(
                sprintf('There is no shop user with %s email verification token', $command->token)
            );
        }

        $user->setVerifiedAt(new \DateTime());
        $user->setEmailVerificationToken(null);
        $user->enable();

        return new JsonResponse([]);
    }
}
