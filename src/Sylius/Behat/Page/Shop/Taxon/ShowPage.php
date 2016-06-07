<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Taxon;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShowPage extends SymfonyPage
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_taxon_show';
    }
}
