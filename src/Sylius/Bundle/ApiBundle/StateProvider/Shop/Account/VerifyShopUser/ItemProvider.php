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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Account\VerifyShopUser;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<VerifyShopUser> */
final readonly class ItemProvider implements ProviderInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): VerifyShopUser
    {
        Assert::true(is_a($operation->getClass(), VerifyShopUser::class, true));
        Assert::isInstanceOf($operation, Patch::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);
        Assert::stringNotEmpty($uriVariables['token'] ?? null, 'Token is required.');

        return new VerifyShopUser($uriVariables['token'], $context[ContextKeys::CHANNEL]->getCode(), $context[ContextKeys::LOCALE_CODE]);
    }
}
