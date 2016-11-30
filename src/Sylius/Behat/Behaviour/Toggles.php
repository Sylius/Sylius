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
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
trait Toggles
{
    /**
     * @return NodeElement
     */
    abstract protected function getToggleableElement();

    /**
     * @throws \RuntimeException If already enabled
     */
    public function enable()
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, false);

        $toggleableElement->check();
    }

    /**
     * @throws \RuntimeException If already disabled
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
            throw new \RuntimeException(sprintf(
                "Toggleable element state is '%s' but expected '%s'.",
                $toggleableElement->isChecked() ? 'true' : 'false',
                $expectedState ? 'true' : 'false'
            ));
        }
    }
}
