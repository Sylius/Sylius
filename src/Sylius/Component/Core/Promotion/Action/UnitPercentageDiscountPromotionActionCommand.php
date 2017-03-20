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

use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitPercentageDiscountConfigurationType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\OrderItemPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderItemPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class UnitPercentageDiscountPromotionActionCommand implements ChannelBasedPromotionActionCommandInterface
{
    const TYPE = 'unit_percentage_discount';

    /**
     * @var OrderItemPromotionAdjustmentsApplicatorInterface
     */
    private $adjustmentsApplicator;

    /**
     * @var OrderItemPromotionAdjustmentsReverserInterface
     */
    private $adjustmentsReverser;

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
     * @param OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
     * @param OrderItemPromotionAdjustmentsReverserInterface $adjustmentsReverser
     * @param FilterInterface $priceRangeFilter
     * @param FilterInterface $taxonFilter
     * @param FilterInterface $productFilter
     */
    public function __construct(
        OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        OrderItemPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter
    ) {
        $this->priceRangeFilter = $priceRangeFilter;
        $this->taxonFilter = $taxonFilter;
        $this->productFilter = $productFilter;
        $this->adjustmentsApplicator = $adjustmentsApplicator;
        $this->adjustmentsReverser = $adjustmentsReverser;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode]) || !isset($configuration[$channelCode]['percentage'])) {
            return false;
        }

        $filteredItems = $this->priceRangeFilter->filter(
            $subject->getItems()->toArray(),
            array_merge(['channel' => $subject->getChannel()], $configuration[$channelCode])
        );
        $filteredItems = $this->taxonFilter->filter($filteredItems, $configuration[$channelCode]);
        $filteredItems = $this->productFilter->filter($filteredItems, $configuration[$channelCode]);

        if (empty($filteredItems)) {
            return false;
        }

        foreach ($filteredItems as $item) {
            $promotionAmount = (int) round($item->getUnitPrice() * $configuration[$channelCode]['percentage']);
            $this->adjustmentsApplicator->apply($item, $promotion, $promotionAmount);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        $this->adjustmentsReverser->revert($subject, $promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return UnitPercentageDiscountConfigurationType::class;
    }
}
