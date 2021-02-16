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

namespace Sylius\Bundle\ApiBundle\EventHandler;

use Sylius\Bundle\ApiBundle\Command\SendShopUserVerificationEmail;
use Sylius\Bundle\ApiBundle\Event\ShopUserRegistered;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/** @experimental */
final class ShopUserRegisteredHandler
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    /** @var GeneratorInterface */
    private $tokenGenerator;

    public function __construct(
        MessageBusInterface $commandBus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $shopUserRepository,
        GeneratorInterface $tokenGenerator
    ) {
        $this->commandBus = $commandBus;
        $this->channelRepository = $channelRepository;
        $this->shopUserRepository = $shopUserRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function __invoke(ShopUserRegistered $shopUserRegistered): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneByEmail($shopUserRegistered->getShopUserEmail());
        if ($shopUser->getEmailVerificationToken() !== null) {
            $this->commandBus->dispatch(new SendShopUserVerificationEmail(
                $shopUserRegistered->getShopUserEmail(),
                $shopUserRegistered->getLocale(),
                $shopUserRegistered->getChannelCode()
            ));
        }
    }
}
