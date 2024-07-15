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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\ShippingMethod;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ShippingMethodInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        Assert::true(is_a($operation->getClass(), ShippingMethodInterface::class, true));
        Assert::isInstanceOf($operation, GetCollection::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        /** @var ChannelInterface $channel */
        $channel = $context['sylius_api_channel'];

        return $this->shippingMethodRepository->findEnabledForChannel($channel);
    }
}
