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

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Sylius\Bundle\ApiBundle\Command\ChangePasswordShopUser;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/** @experimental */
class ChangePasswordShopUserHandler implements MessageHandlerInterface
{
    /** @var DataPersisterInterface */
    private $dataPersister;

    /** @var PasswordUpdaterInterface */
    private $passwordUpdater;

    /** @var UserContextInterface */
    private $userContext;

    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    public function __construct(
        DataPersisterInterface $dataPersister,
        PasswordUpdaterInterface $passwordUpdater,
        UserContextInterface $userContext,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->dataPersister = $dataPersister;
        $this->passwordUpdater = $passwordUpdater;
        $this->userContext = $userContext;
        $this->encoderFactory = $encoderFactory;
    }

    public function __invoke(ChangePasswordShopUser $changePasswordShopUser){

        if ($changePasswordShopUser->confirmPassword !== $changePasswordShopUser->password) {
            throw new \RuntimeException("Your password and confirmation password does not match.");
        }

        /** @var ShopUser $user */
        $user = $this->userContext->getUser();

        $encoder = $this->encoderFactory->getEncoder($user);

        if (!$encoder->isPasswordValid($user->getPassword(), $changePasswordShopUser->oldPassword, $user->getSalt())) {
            throw new \RuntimeException("Could not authenticate. Your old password is different then the provided");
        }

        if (in_array('ROLE_USER', $user->getRoles(), true)) {
            $this->passwordUpdater->updatePassword($user);
            $this->dataPersister->persist($user);
        }
    }
}
