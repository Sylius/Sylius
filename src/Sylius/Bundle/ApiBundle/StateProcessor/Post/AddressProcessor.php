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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<AddressInterface>
 */
final readonly class AddressProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserContextInterface $userContext,
    ) {
    }

    /**
     * @param AddressInterface $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, AddressInterface::class);
        Assert::isInstanceOf($operation, Post::class);

        $user = $this->userContext->getUser();
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;

        if (null !== $customer) {
            $data->setCustomer($customer);

            if (null === $customer->getDefaultAddress()) {
                $customer->setDefaultAddress($data);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}