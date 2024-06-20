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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\PromotionCouponInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function filterByCode(string $code): void
    {
        $this->getElement('code_filter')->setValue($code);
    }

    public function getUsedNumber(string $promotionCouponCode): int
    {
        $used = $this->getCellForResource('used', ['code' => $promotionCouponCode]);

        return (int) $used->find('css', '[data-test-used]')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code_filter' => '#criteria_code_value',
        ]);
    }
}
