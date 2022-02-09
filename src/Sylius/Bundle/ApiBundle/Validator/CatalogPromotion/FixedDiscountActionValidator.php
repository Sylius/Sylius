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

namespace Sylius\Bundle\ApiBundle\Validator\CatalogPromotion;

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class FixedDiscountActionValidator implements ActionValidatorInterface
{
    public function __construct(
        private ActionValidatorInterface $baseActionValidator,
        private SectionProviderInterface $sectionProvider
    ) {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        if (!$this->sectionProvider->getSection() instanceof AdminApiSection) {
            $this->baseActionValidator->validate($configuration, $constraint, $context);

            return;
        }

        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        if (empty($configuration)) {
            $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

            return;
        }

        $this->baseActionValidator->validate($configuration, $constraint, $context);

        foreach ($configuration as $channelConfiguration) {
            if ($this->isChannelAmountValid($channelConfiguration)) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

                return;
            }
        }
    }

    private function isChannelAmountValid(array $channelConfiguration): bool
    {
        return
            !isset($channelConfiguration['amount']) ||
            !is_integer($channelConfiguration['amount']) ||
            $channelConfiguration['amount'] < 0
        ;
    }
}
