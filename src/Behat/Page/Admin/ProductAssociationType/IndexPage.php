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

namespace Sylius\Behat\Page\Admin\ProductAssociationType;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function specifyFilterType(string $field, string $type): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $type);
    }

    public function specifyFilterValue(string $field, string $value): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $value);
    }
}
