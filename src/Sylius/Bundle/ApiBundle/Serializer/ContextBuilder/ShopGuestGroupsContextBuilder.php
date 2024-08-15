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

namespace Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class ShopGuestGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public const SYLIUS_SHOP_GUEST_READ = 'sylius:shop:guest:read';

    public const SYLIUS_SHOP_GUEST_WRITE = 'sylius:shop:guest:write';

    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
        private SectionProviderInterface $sectionProvider,
        private UserContextInterface $userContext,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return $context;
        }

        if (null === $this->userContext->getUser() && isset($context['groups'])) {
            $context['groups'][] = $normalization ? self::SYLIUS_SHOP_GUEST_READ : self::SYLIUS_SHOP_GUEST_WRITE;
        }

        return $context;
    }
}
