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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getCouponCodes(): iterable
    {
        $codeCells = $this->getDocument()->findAll('css', '.sylius-grid-wrapper tbody tr td:nth-child(2)');

        /** @var NodeElement $codeCell */
        foreach ($codeCells as $codeCell) {
            yield $codeCell->getText();
        }
    }
}
