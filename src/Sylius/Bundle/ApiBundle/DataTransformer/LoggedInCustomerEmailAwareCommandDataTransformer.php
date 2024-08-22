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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

final class LoggedInCustomerEmailAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /**
     * @param mixed $object
     * @param array<string, mixed> $context
     */
    public function transform($object, string $to, array $context = []): object
    {
        $customer = $this->getCustomer();

        /** @var CustomerEmailAwareInterface|mixed $object */
        Assert::isInstanceOf($object, CustomerEmailAwareInterface::class);

        if ($customer !== null) {
            $object->setEmail($customer->getEmail());
        }

        return $object;
    }

    /**
     * @param mixed $object
     */
    public function supportsTransformation($object): bool
    {
        return $object instanceof CustomerEmailAwareInterface;
    }

    private function getCustomer(): ?CustomerInterface
    {
        $user = $this->userContext->getUser();

        if (!$user instanceof ShopUserInterface) {
            return null;
        }

        $customer = $user->getCustomer();
        Assert::nullOrIsInstanceOf($customer, CustomerInterface::class);

        return $customer;
    }
}
