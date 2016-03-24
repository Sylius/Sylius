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
use Sylius\Behat\Page\ElementNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
trait Toggles
{
    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    abstract protected function getToggleableElement();

    /**
     * @throws ElementNotFoundException
     * @throws \RuntimeException
     */
    public function enable()
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, false);

        $toggleableElement->check();
    }

    /**
     * @throws ElementNotFoundException
     * @throws \RuntimeException
     */
    public function disable()
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, true);

        $toggleableElement->uncheck();
    }

    /**
     * @param NodeElement $toggleableElement
     * @param bool $expectedState
     *
     * @throws \RuntimeException
     */
    private function assertCheckboxState(NodeElement $toggleableElement, $expectedState)
    {
        if ($toggleableElement->isChecked() !== $expectedState) {
            throw new \RuntimeException('Toggleable element state %s but expected %s.', $toggleableElement->isChecked(), $expectedState);
        }
    }
}
