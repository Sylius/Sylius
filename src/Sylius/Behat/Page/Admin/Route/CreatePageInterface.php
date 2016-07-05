<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Route;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @param string $title
     */
    public function chooseContent($title);
}
