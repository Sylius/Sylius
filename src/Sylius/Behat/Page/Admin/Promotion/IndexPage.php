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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Promotion\Model\PromotionInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getUsageNumber(PromotionInterface $promotion): int
    {
        $usage = $this->getPromotionFieldsWithHeader($promotion, 'usage');

        return (int) $usage->find('css', 'span:first-child')->getText();
    }

    public function isAbleToManageCouponsFor(PromotionInterface $promotion): bool
    {
        $actions = $this->getPromotionFieldsWithHeader($promotion, 'actions');

        return $actions->hasLink('List coupons');
    }

    public function isCouponBasedFor(PromotionInterface $promotion): bool
    {
        $coupons = $this->getPromotionFieldsWithHeader($promotion, 'couponBased');

        return 'Yes' === $coupons->getText();
    }

    private function getPromotionFieldsWithHeader(PromotionInterface $promotion, string $header): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $fields = $tableAccessor->getFieldFromRow($table, $tableAccessor->getRowWithFields($table, ['code' => $promotion->getCode()]), $header);

        return $fields;
    }
}
