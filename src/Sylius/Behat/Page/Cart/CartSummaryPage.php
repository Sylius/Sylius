<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Cart;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPage extends SymfonyPage
{
    public function openPage()
    {
        $url = $this->router->generate($this->getRouteName());
        $this->getSession()->visit($url);
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_cart_summary';
    }
}
