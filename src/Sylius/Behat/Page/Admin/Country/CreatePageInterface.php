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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function chooseName($name);

    /**
     * @param string $name
     */
    public function selectName($name);

    /**
     * @param string $name
     * @param string $code
     * @param string $abbreviation
     */
    public function fillProvinceData($name, $code, $abbreviation);
}
