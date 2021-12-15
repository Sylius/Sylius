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

namespace Sylius\Bundle\CoreBundle\Validator\CatalogPromotionAction;

use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class FixedDiscountActionValidator implements ActionValidatorInterface
{
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
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

        foreach ($catalogPromotionAction->getCatalogPromotion()->getChannels() as $channel) {
            if (!$this->isChannelConfigured($channel->getCode(), $configuration)) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.channel_not_configured')->atPath('configuration.actions')->addViolation();

                return;
            }
        }

        foreach ($configuration as $channelCode => $channelConfiguration) {
            if (null === $this->channelRepository->findOneBy(['code' => $channelCode])) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.invalid_channel')->atPath('configuration')->addViolation();

                return;
            }

            if (!array_key_exists('amount', $channelConfiguration) || !is_integer($channelConfiguration['amount']) || $channelConfiguration['amount'] < 0) {
                $context->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->atPath('configuration')->addViolation();

                return;
            }
        }
    }

    private function isChannelConfigured(string $channelCode, array $configuration): bool
    {
        if (array_key_exists($channelCode, $configuration) && $configuration[$channelCode] > 0) {
            return true;
        }

        return false;
    }
}
