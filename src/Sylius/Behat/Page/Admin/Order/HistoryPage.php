<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class HistoryPage extends SymfonyPage implements HistoryPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_order_history';
    }

    /**
     * @return int
     */
    public function countShippingAddressChanges()
    {
        return count($this->getDocument()->findAll('css', '#shipping-address-changes tbody tr'));
    }
}
