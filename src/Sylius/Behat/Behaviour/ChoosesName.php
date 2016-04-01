<?php

/*
 * This file is part of the Sylius package.
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
trait ChoosesName
{
    use DocumentAccessor;

    /**
     * @param string $name
     */
    public function chooseName($name)
    {
        $this->getDocument()->selectFieldOption('Name', $name);
    }
}
