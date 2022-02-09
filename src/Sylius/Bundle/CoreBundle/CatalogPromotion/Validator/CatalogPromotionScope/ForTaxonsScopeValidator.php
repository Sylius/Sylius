<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\Constraints\CatalogPromotionScope;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class ForTaxonsScopeValidator implements ScopeValidatorInterface
{
    public function __construct(private TaxonRepositoryInterface $taxonRepository)
    {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        foreach ($configuration['taxons'] as $taxonCode) {
            if (null === $this->taxonRepository->findOneBy(['code' => $taxonCode])) {
                $context->buildViolation($constraint->invalidTaxons)->atPath('configuration.taxons')->addViolation();

                return;
            }
        }
    }
}
