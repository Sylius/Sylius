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

namespace Sylius\Bundle\ApiBundle\Validator\CatalogPromotion;

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class ForVariantsScopeValidator implements ScopeValidatorInterface
{
    public function __construct(
        private ScopeValidatorInterface $baseScopeValidator,
        private SectionProviderInterface $sectionProvider,
    ) {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        if (
            $this->sectionProvider->getSection() instanceof AdminApiSection &&
            (!isset($configuration['variants']) || empty($configuration['variants']))
        ) {
            $context
                ->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')
                ->atPath('configuration.variants')
                ->addViolation()
            ;

            return;
        }

        $this->baseScopeValidator->validate($configuration, $constraint, $context);
    }
}
