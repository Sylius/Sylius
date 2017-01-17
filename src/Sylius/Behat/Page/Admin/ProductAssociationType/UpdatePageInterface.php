<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductAssociationType;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameItIn($name, $languageCode);

    /**
     * @return bool
     */
    public function isCodeDisabled();
}
