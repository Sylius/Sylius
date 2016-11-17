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
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class FixedDiscountPromotionActionCommand extends DiscountPromotionActionCommand
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
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
     * @param UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        CurrencyConverterInterface $currencyConverter
    ) {
        $this->proportionalDistributor = $proportionalIntegerDistributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
        $this->currencyConverter = $currencyConverter;
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

        $promotionAmount = $this->calculateAdjustmentAmount(
            $subject->getPromotionSubjectTotal(),
            $this->getAmountByCurrencyCode($configuration, $subject->getCurrencyCode())
        );
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
        Assert::keyExists($configuration, 'base_amount');
        Assert::integer($configuration['base_amount']);
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

    /**
     * @param array $configuration
     * @param string $currencyCode
     *
     * @return int
     */
    private function getAmountByCurrencyCode(array $configuration, $currencyCode)
    {
        if (!isset($configuration['amounts'][$currencyCode])) {
            return $configuration['base_amount'];
        }

        return $this->currencyConverter->convertToBase(
            $configuration['amounts'][$currencyCode],
            $currencyCode
        );
    }
}
