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

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Command\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class RegisterShopUserHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $shopUserFactory;

    /** @var ObjectManager */
    private $shopUserManager;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerProviderInterface $customerProvider,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->shopUserFactory = $shopUserFactory;
        $this->shopUserManager = $shopUserManager;
        $this->customerProvider = $customerProvider;
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(RegisterShopUser $command): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->shopUserFactory->createNew();
        $user->setPlainPassword($command->password);

        $customer = $this->customerProvider->provide($command->email);

        if ($customer->getUser() !== null) {
            throw new \DomainException(sprintf('User with email "%s" is already registered.', $command->email));
        }

        $customer->setFirstName($command->firstName);
        $customer->setLastName($command->lastName);
        $customer->setPhoneNumber($command->phoneNumber);
        $customer->setUser($user);

        $this->handleVerificationInChannel($user, $command->channelCode);

        $this->shopUserManager->persist($user);
    }

    private function handleVerificationInChannel(ShopUserInterface $user, ?string $channelCode): void
    {
        Assert::notNull($channelCode);

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (!$channel->isAccountVerificationRequired()) {
            $user->setEnabled(true);
        }
    }
}
