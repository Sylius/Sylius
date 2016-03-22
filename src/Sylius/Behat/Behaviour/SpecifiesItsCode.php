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
trait SpecifiesItsCode
{
    use DocumentAccessor;

    /**
     * @param string $code
     */
    public function specifyCode($code)
    {
        $this->getDocument()->fillField('Code', $code);
    }
}
