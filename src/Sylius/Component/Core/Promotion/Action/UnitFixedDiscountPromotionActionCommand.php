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

use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitFixedDiscountConfigurationType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UnitFixedDiscountPromotionActionCommand extends UnitDiscountPromotionActionCommand
{
    const TYPE = 'unit_fixed_discount';

    /**
     * @var FilterInterface
     */
    private $priceRangeFilter;

    /**
     * @var FilterInterface
     */
    private $taxonFilter;

    /**
     * @var FilterInterface
     */
    private $productFilter;

    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param FilterInterface $priceRangeFilter
     * @param FilterInterface $taxonFilter
     * @param FilterInterface $productFilter
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        CurrencyConverterInterface $currencyConverter
    ) {
        parent::__construct($adjustmentFactory);

        $this->priceRangeFilter = $priceRangeFilter;
        $this->taxonFilter = $taxonFilter;
        $this->productFilter = $productFilter;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $amount = $this->getAmountByCurrencyCode($configuration, $subject->getCurrencyCode());
        if (0 === $amount) {
            return;
        }

        $filteredItems = $this->priceRangeFilter->filter(
            $subject->getItems()->toArray(),
            array_merge($configuration, ['channel' => $subject->getChannel()])
        );
        $filteredItems = $this->taxonFilter->filter($filteredItems, $configuration);
        $filteredItems = $this->productFilter->filter($filteredItems, $configuration);

        foreach ($filteredItems as $item) {
            $this->setUnitsAdjustments($item, $amount, $promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return UnitFixedDiscountConfigurationType::class;
    }

    /**
     * @param OrderItemInterface $item
     * @param int $amount
     * @param PromotionInterface $promotion
     */
    private function setUnitsAdjustments(OrderItemInterface $item, $amount, PromotionInterface $promotion)
    {
        foreach ($item->getUnits() as $unit) {
            $this->addAdjustmentToUnit(
                $unit,
                min($unit->getTotal(), $amount),
                $promotion
            );
        }
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
