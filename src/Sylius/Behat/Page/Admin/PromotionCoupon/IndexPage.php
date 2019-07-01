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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getCouponCodes(): iterable
    {
        /** @var NodeElement $codeCell */
        foreach ($this->getDocument()->findAll('css', '.sylius-grid-wrapper tbody tr td:nth-child(2)') as $codeCell) {
            yield $codeCell->getText();
        }
    }
}
