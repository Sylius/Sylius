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
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class FixedDiscountActionValidator implements ActionValidatorInterface
{
    public function __construct(
        private ActionValidatorInterface $baseActionValidator,
        private SectionProviderInterface $sectionProvider,
    ) {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        if (!$this->sectionProvider->getSection() instanceof AdminApiSection) {
            $this->baseActionValidator->validate($configuration, $constraint, $context);

            return;
        }

        if (empty($configuration)) {
            $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

            return;
        }

        foreach ($configuration as $channelConfiguration) {
            if ($this->isChannelAmountValid($channelConfiguration)) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

                return;
            }
        }

        $this->baseActionValidator->validate($configuration, $constraint, $context);
    }

    private function isChannelAmountValid(array $channelConfiguration): bool
    {
        return
            !isset($channelConfiguration['amount']) ||
            !is_int($channelConfiguration['amount']) ||
            $channelConfiguration['amount'] < 0
        ;
    }
}
