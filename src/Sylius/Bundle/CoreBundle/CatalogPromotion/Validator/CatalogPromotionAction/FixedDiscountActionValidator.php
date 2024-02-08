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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionAction;

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
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        if (empty($configuration)) {
            $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

            return;
        }

        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $context->getObject();

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $catalogPromotionAction->getCatalogPromotion();

        foreach ($catalogPromotion->getChannels() as $channel) {
            if (!$this->isChannelConfigured($channel->getCode(), $configuration)) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.channel_not_configured')->atPath('configuration')->addViolation();

                return;
            }
        }

        foreach ($configuration as $channelCode => $channelConfiguration) {
            if (null === $this->channelRepository->findOneBy(['code' => $channelCode])) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.invalid_channel')->atPath('configuration')->addViolation();

                return;
            }
        }
    }

    private function isChannelConfigured(string $channelCode, array $configuration): bool
    {
        return isset($configuration[$channelCode]) && isset($configuration[$channelCode]['amount']);
    }
}
