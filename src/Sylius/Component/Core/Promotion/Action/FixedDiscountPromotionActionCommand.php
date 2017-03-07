<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\OrderPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class FixedDiscountPromotionActionCommand implements ChannelBasedPromotionActionCommandInterface
{
    const TYPE = 'order_fixed_discount';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $proportionalDistributor;

    /**
     * @var OrderPromotionAdjustmentsApplicatorInterface
     */
    private $adjustmentsApplicator;

    /**
     * @var OrderPromotionAdjustmentsReverserInterface
     */
    private $adjustmentsReverser;

    /**
     * @param ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
     * @param OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
     * @param OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser
     */
    public function __construct(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser
    ) {
        $this->proportionalDistributor = $proportionalIntegerDistributor;
        $this->adjustmentsApplicator = $adjustmentsApplicator;
        $this->adjustmentsReverser = $adjustmentsReverser;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        if($subject->countItems() === 0) {
            return false;
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        try {
            $this->isConfigurationValid($configuration[$channelCode]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        $promotionAmount = $this->calculateAdjustmentAmount(
            $subject->getPromotionSubjectTotal(),
            $configuration[$channelCode]['amount']
        );

        if (0 === $promotionAmount) {
            return false;
        }

        $itemsTotals = [];
        foreach ($subject->getItems() as $item) {
            $itemsTotals[] = $item->getTotal();
        }

        $splitPromotion = $this->proportionalDistributor->distribute($itemsTotals, $promotionAmount);
        $this->adjustmentsApplicator->apply($subject, $promotion, $splitPromotion);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        if ($subject->countItems() === 0) {
            return;
        }

        $this->adjustmentsReverser->revert($subject, $promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return FixedDiscountConfigurationType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function isConfigurationValid(array $configuration)
    {
        Assert::keyExists($configuration, 'amount');
        Assert::integer($configuration['amount']);
    }

    /**
     * @param int $promotionSubjectTotal
     * @param int $targetPromotionAmount
     *
     * @return int
     */
    private function calculateAdjustmentAmount($promotionSubjectTotal, $targetPromotionAmount)
    {
        return -1 * min($promotionSubjectTotal, $targetPromotionAmount);
    }
}
