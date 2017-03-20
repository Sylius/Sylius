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

use Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\OrderPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class PercentageDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    const TYPE = 'order_percentage_discount';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var OrderPromotionAdjustmentsApplicatorInterface
     */
    private $adjustmentsApplicator;

    /**
     * @var OrderPromotionAdjustmentsReverserInterface
     */
    private $adjustmentsReverser;

    /**
     * @param ProportionalIntegerDistributorInterface $distributor
     * @param OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
     * @param OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser
     */
    public function __construct(
        ProportionalIntegerDistributorInterface $distributor,
        OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser
    ) {
        $this->distributor = $distributor;
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

        try {
            $this->isConfigurationValid($configuration);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        $promotionAmount = $this->calculateAdjustmentAmount($subject->getPromotionSubjectTotal(), $configuration['percentage']);
        if (0 === $promotionAmount) {
            return false;
        }

        $itemsTotal = [];
        foreach ($subject->getItems() as $orderItem) {
            $itemsTotal[] = $orderItem->getTotal();
        }

        $splitPromotion = $this->distributor->distribute($itemsTotal, $promotionAmount);
        $this->adjustmentsApplicator->apply($subject, $promotion, $splitPromotion);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        if($subject->countItems() === 0) {
            return false;
        }

        $this->adjustmentsReverser->revert($subject, $promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return PercentageDiscountConfigurationType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function isConfigurationValid(array $configuration)
    {
        if (!isset($configuration['percentage']) || !is_float($configuration['percentage'])) {
            throw new \InvalidArgumentException('"percentage" must be set and must be a float.');
        }
    }

    /**
     * @param int $promotionSubjectTotal
     * @param int $percentage
     *
     * @return int
     */
    private function calculateAdjustmentAmount($promotionSubjectTotal, $percentage)
    {
        return -1 * (int) round($promotionSubjectTotal * $percentage);
    }
}
