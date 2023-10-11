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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailIfNotSetAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final readonly class LoggedInCustomerEmailIfNotSetAwareInputDataProcessor implements InputDataProcessorInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->getCustomer();

        /** @var CustomerEmailAwareInterface|mixed $data */
        Assert::isInstanceOf($data, LoggedInCustomerEmailIfNotSetAwareInterface::class);

        if ($customer === null) {
            return [$data, $operation, $uriVariables, $context];
        }

        if ($data->getEmail() === null) {
            $data->setEmail($customer->getEmail());
        }

        return [$data, $operation, $uriVariables, $context];
    }

    public function supports(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): bool
    {
        return $data instanceof LoggedInCustomerEmailIfNotSetAwareInterface;
    }

    private function getCustomer(): ?CustomerInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();
            Assert::nullOrIsInstanceOf($customer, CustomerInterface::class);

            return $customer;
        }

        return null;
    }
}
