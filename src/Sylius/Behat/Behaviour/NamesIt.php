<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Behaviour;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
trait NamesIt
{
    use DocumentAccessor;

    /**
     * @param string $name
     */
    public function nameIt($name)
    {
        $this->getDocument()->fillField('Name', $name);
    }
}
