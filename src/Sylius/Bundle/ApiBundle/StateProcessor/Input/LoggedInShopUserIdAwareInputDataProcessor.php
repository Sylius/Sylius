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
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final readonly class LoggedInShopUserIdAwareInputDataProcessor implements InputDataProcessorInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        Assert::isInstanceOf($data, ShopUserIdAwareInterface::class);

        $user = $this->getShopUser();

        if (null !== $user) {
            $data->setShopUserId($user->getId());
        }

        return [$data, $operation, $uriVariables, $context];
    }

    public function supports(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): bool
    {
        return $data instanceof ShopUserIdAwareInterface;
    }

    private function getShopUser(): ?ShopUserInterface
    {
        $user = $this->userContext->getUser();

        return $user instanceof ShopUserInterface ? $user : null;
    }
}
