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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @implements ProcessorInterface<object, mixed> */
final readonly class PersistProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<object, mixed> $processor
     * @param UriTemplateParentResourceResolverInterface<PromotionInterface> $uriTemplateParentResourceResolver
     */
    public function __construct(
        private ProcessorInterface $processor,
        private UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver,
        private ValidatorInterface $validator,
    ) {
    }

    /** @param PromotionCouponInterface $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$operation instanceof Post) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $data->setPromotion(
            $this->uriTemplateParentResourceResolver->resolve($data, $operation, $context),
        );

        $violations = $this->validator->validate(value: $data, groups: $operation->getValidationContext()['groups'] ?? []);

        if (0 !== \count($violations)) {
            throw new ValidationException($violations);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
