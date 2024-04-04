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

namespace Sylius\Bundle\ApiBundle\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * @implements ProviderInterface<ResetPassword>
 */
final readonly class ShopUserResetPasswordProvider implements ProviderInterface
{
    /**
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ResetPassword
    {
        Assert::same($operation->getClass(), ResetPassword::class);

        if ($operation instanceof Patch) {
            return new ResetPassword($uriVariables['token']);
        }

        throw new \RuntimeException('Only PATCH operation is supported.');
    }
}
