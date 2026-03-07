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

namespace Sylius\Bundle\AdminBundle\Twig\Context\Factory;

use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Option\RequestOption;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\Twig\Context\Factory\ContextFactoryInterface;

/**
 * @experimental
 */
final readonly class PromotionTwigContextFactory implements ContextFactoryInterface
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
    ) {
    }

    public function create(mixed $data, Operation $operation, Context $context): array
    {
        $request = $context->get(RequestOption::class)?->request();

        if (null === $request) {
            return [];
        }

        $promotionId = $request->attributes->get('promotionId');

        $promotion = $this->promotionRepository->find($promotionId);

        return [
            'promotion' => $promotion,
        ];
    }
}
