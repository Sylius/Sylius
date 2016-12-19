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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface HistoryPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function countShippingAddressChanges();
}
