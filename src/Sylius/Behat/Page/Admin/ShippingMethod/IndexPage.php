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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function chooseArchival($isArchival)
    {
        $this->getElement('filter_archival')->selectOption($isArchival);
    }

    /**
     * {@inheritdoc}
     */
    public function isArchivalFilterEnabled()
    {
        $archival = $this->getDocument()->find('css', 'button:contains("Restore")');

        return null !== $archival;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_archival' => '#criteria_archival',
        ]);
    }
}
