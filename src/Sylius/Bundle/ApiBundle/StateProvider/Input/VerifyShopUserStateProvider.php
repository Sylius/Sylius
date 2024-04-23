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

namespace Sylius\Bundle\ApiBundle\StateProvider\Input;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * @implements ProviderInterface<VerifyShopUser>
 */
final class VerifyShopUserStateProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!is_a($operation->getClass(), VerifyShopUser::class, true)) {
            return null;
        }

        Assert::stringNotEmpty($uriVariables['token'] ?? null, 'Token is required.');

        return new VerifyShopUser($uriVariables['token'], $context[ContextKeys::CHANNEL]->getCode(), $context[ContextKeys::LOCALE_CODE],);
    }
}
