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

use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class FixedDiscountAction extends DiscountAction
{
    const TYPE = 'order_fixed_discount';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $proportionalDistributor;

    /**
     * @var UnitsPromotionAdjustmentsApplicatorInterface
     */
    private $unitsPromotionAdjustmentsApplicator;

    /**
     * {@inheritdoc}
     *
     * @param ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
     * @param UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
     */
    public function __construct(
        OriginatorInterface $originator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        parent::__construct($originator);

        $this->proportionalDistributor = $proportionalIntegerDistributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$this->isSubjectValid($subject)) {
            return;
        }

        $this->isConfigurationValid($configuration);

        $promotionAmount = $this->calculateAdjustmentAmount($subject->getPromotionSubjectTotal(), $configuration['amount']);
        if (0 === $promotionAmount) {
            return;
        }

        $itemsTotals = [];
        foreach ($subject->getItems() as $item) {
            $itemsTotals[] = $item->getTotal();
        }

        $splitPromotion = $this->proportionalDistributor->distribute($itemsTotals, $promotionAmount);
        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_fixed_discount_configuration';
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
