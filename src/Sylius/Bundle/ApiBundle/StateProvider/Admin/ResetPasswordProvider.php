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

namespace Sylius\Bundle\ApiBundle\StateProvider\Admin;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Webmozart\Assert\Assert;

/** @experimental */
final class ResetPasswordProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ResetPassword
    {
        Assert::true(is_a($operation->getClass(), ResetPassword::class, true));

        if ($operation instanceof Patch) {
            return new ResetPassword($uriVariables['token']);
        }

        throw new \RuntimeException('Only PATCH operation is supported.');
    }
}
