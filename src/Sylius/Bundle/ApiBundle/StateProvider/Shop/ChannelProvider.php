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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<ChannelInterface>
 */
final readonly class ChannelProvider implements ProviderInterface
{
    public function __construct(private readonly SectionProviderInterface $sectionProvider)
    {
    }

    /**
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        Assert::same($operation->getClass(), Channel::class);

        if ($this->isShopGetCollectionOperation($operation) && isset($context['sylius_api_channel'])) {
            return [$context['sylius_api_channel']];
        }

        throw new \RuntimeException('Only Shop GET collection operation is supported.');
    }

    private function isShopGetCollectionOperation(Operation $operation): bool
    {
        return $operation instanceof GetCollection && $this->sectionProvider->getSection() instanceof ShopApiSection;
    }
}
