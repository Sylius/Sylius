<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUsedThemeName($channelCode)
    {
        $table = $this->getDocument()->find('css', 'table');

        $row = $this->getTableAccessor()->getRowWithFields($table, ['code' => $channelCode]);

        return trim($this->getTableAccessor()->getFieldFromRow($table, $row, 'themeName')->getText());
    }
}
