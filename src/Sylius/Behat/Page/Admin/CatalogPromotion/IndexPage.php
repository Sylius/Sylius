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

namespace Sylius\Behat\Page\Admin\CatalogPromotion;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseArchivalFilter(string $isArchival): void
    {
        $this->getElement('filter_archival')->selectOption($isArchival);
    }

    public function isArchivalFilterEnabled(): bool
    {
        $archival = $this->getDocument()->find('css', 'button:contains("Restore")');

        return null !== $archival;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_archival' => '#criteria_archival',
        ]);
    }
}
