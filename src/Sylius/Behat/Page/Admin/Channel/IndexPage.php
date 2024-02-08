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

namespace Sylius\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getUsedThemeName(string $channelCode): ?string
    {
        $table = $this->getDocument()->find('css', 'table');

        $row = $this->getTableAccessor()->getRowWithFields($table, ['code' => $channelCode]);

        return trim($this->getTableAccessor()->getFieldFromRow($table, $row, 'themeName')->getText());
    }
}
