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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Webmozart\Assert\Assert;

final class AccountResetPasswordItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    /**
     * @param string $id
     * @param array<string, mixed> $context
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ResetPassword
    {
        Assert::string($id);

        return new ResetPassword($id);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ResetPassword::class, true);
    }
}
