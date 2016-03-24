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

use Behat\Mink\Element\NodeElement;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
trait ChecksCodeImmutability
{
    /**
     * @return NodeElement
     */
    abstract protected function getCodeElement();

    /**
     * @return bool
     */
    public function isCodeDisabled()
    {
        return 'disabled' === $this->getCodeElement()->getAttribute('disabled');
    }
}
