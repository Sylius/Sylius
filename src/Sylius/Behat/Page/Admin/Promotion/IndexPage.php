<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * @param PromotionInterface $promotion
     *
     * @return int
     */
    public function getUsageNumber(PromotionInterface $promotion)
    {
        $usage = $this->getPromotionFieldsWithHeader($promotion, 'usage');

        return (int) $usage->find('css', 'span:first-child')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isAbleToManageCouponsFor(PromotionInterface $promotion)
    {
        $actions = $this->getPromotionFieldsWithHeader($promotion, 'actions');

        return $actions->hasLink('List coupons');
    }

    /**
     * {@inheritdoc}
     */
    public function isCouponBasedFor(PromotionInterface $promotion)
    {
        $coupons = $this->getPromotionFieldsWithHeader($promotion, 'couponBased');

        return 'Yes' === $coupons->getText();
    }

    /**
     * @param PromotionInterface $promotion
     * @param string $header
     *
     * @return NodeElement
     */
    private function getPromotionFieldsWithHeader(PromotionInterface $promotion, $header)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $fields = $tableAccessor->getFieldFromRow($table, $tableAccessor->getRowWithFields($table, ['code' => $promotion->getCode()]), $header);

        return $fields;
    }
}
