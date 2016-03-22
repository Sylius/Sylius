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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\ElementNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'enabled' => '#sylius_country_enabled',
        'code' => '#sylius_country_code',
    ];

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $enabled = $this->getElement('enabled');
        $this->assertPriorStateOfToggleableElement($enabled, false);

        $enabled->check();
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $enabled = $this->getElement('enabled');
        $this->assertPriorStateOfToggleableElement($enabled, true);

        $enabled->uncheck();
    }

    /**
     * {@inheritdoc}
     */
    public function isCodeFieldDisabled()
    {
        try {
            $codeField = $this->getElement('code');
            return $codeField->getAttribute('disabled') === 'disabled';
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * @param NodeElement $toggleableElement
     * @param bool $expectedState
     *
     * @throws \RuntimeException
     */
    private function assertPriorStateOfToggleableElement(NodeElement $toggleableElement, $expectedState)
    {
        if ($toggleableElement->isChecked() !== $expectedState) {
            throw new \RuntimeException('Toggleable element state %s but expected %s', $toggleableElement->isChecked(), $expectedState);
        }
    }
}
