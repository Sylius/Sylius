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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable();
    public function disable();

    /**
     * @return bool
     */
    public function isCodeFieldDisabled();

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function isThereProvince($provinceName);

    /**
     * @param string $provinceName
     */
    public function removeProvince($provinceName);
}
