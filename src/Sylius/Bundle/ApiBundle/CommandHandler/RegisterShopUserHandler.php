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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RegisterShopUserHandler implements MessageHandlerInterface
{
    /** @var CanonicalizerInterface */
    protected $canonicalizer;

    /** @var FactoryInterface */
    protected $shopUserFactory;

    /** @var FactoryInterface */
    protected $customerFactory;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /** @var ObjectManager */
    protected $shopUserManager;

    public function __construct(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $shopUserManager
    ) {
        $this->canonicalizer = $canonicalizer;
        $this->shopUserFactory = $shopUserFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->shopUserManager = $shopUserManager;
    }

    public function __invoke(RegisterShopUser $command): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->shopUserFactory->createNew();
        $user->setPlainPassword($command->password);

        $customer = $this->provideCustomer($command->email);
        $customer->setFirstName($command->firstName);
        $customer->setLastName($command->lastName);
        $customer->setEmail($command->email);
        $customer->setPhoneNumber($command->phoneNumber);
        $customer->setUser($user);

        $this->shopUserManager->persist($user);
    }

    protected function provideCustomer(string $email): CustomerInterface
    {
        $emailCanonical = $this->canonicalizer->canonicalize($email);

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

        if ($customer === null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
        }

        if ($customer->getUser() !== null) {
            throw new \DomainException(sprintf('User with email "%s" is already registered.', $emailCanonical));
        }

        return $customer;
    }
}
