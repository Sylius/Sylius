<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'enabled' => '#sylius_country_enabled',
    ];

    public function enable()
    {
        $this->getElement('enabled')->check();
    }

    public function disable()
    {
        $this->getElement('enabled')->uncheck();
    }
}
