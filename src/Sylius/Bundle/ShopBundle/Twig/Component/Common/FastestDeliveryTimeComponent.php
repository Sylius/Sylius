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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class FastestDeliveryTimeComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    private const TRANS_EXACT = 'sylius.shop.delivery_time.exact';

    private const TRANS_RANGE = 'sylius.shop.delivery_time.range';

    public function __construct(
        private readonly RepositoryInterface $shippingMethodRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[ExposeInTemplate('text')]
    public function text(): ?string
    {
        /** @var iterable<ShippingMethodInterface> $methods */
        $methods = $this->shippingMethodRepository->findBy(['enabled' => true]);

        $best = $this->findBestDeliveryRange($methods);

        if ($best === null) {
            return null;
        }

        if ($best['minimumDays'] === $best['maximumDays']) {
            return $this->translator->trans(self::TRANS_EXACT, ['%count%' => $best['minimumDays']]);
        }

        return $this->translator->trans(self::TRANS_RANGE, ['%min%' => $best['minimumDays'], '%max%' => $best['maximumDays']]);
    }

    /**
     * @param iterable<ShippingMethodInterface> $methods
     *
     * @return array{minimumDays:int, maximumDays:int}|null
     */
    private function findBestDeliveryRange(iterable $methods): ?array
    {
        $best = null;

        foreach ($methods as $method) {
            $range = $this->normalizeDeliveryRange($method->getMinDeliveryTimeDays(), $method->getMaxDeliveryTimeDays());
            if ($range === null) {
                continue;
            }

            [$minimumDays, $maximumDays] = $range;

            if (
                $best === null ||
                $maximumDays < $best['maximumDays'] ||
                ($maximumDays === $best['maximumDays'] && $minimumDays < $best['minimumDays'])
            ) {
                $best = ['minimumDays' => $minimumDays, 'maximumDays' => $maximumDays];
            }
        }

        return $best;
    }

    /**
     * @return array{0:int,1:int}|null
     */
    private function normalizeDeliveryRange(?int $minDays, ?int $maxDays): ?array
    {
        if ($minDays === null && $maxDays === null) {
            return null;
        }

        if ($minDays !== null && $maxDays !== null) {
            return [min($minDays, $maxDays), max($minDays, $maxDays)];
        }

        $value = $minDays ?? $maxDays ?? 0;

        return [$value, $value];
    }
}
