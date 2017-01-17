<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Zone;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;

    public function addMember()
    {
        $this->getDocument()->clickLink('Add member');
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageForMembers($message)
    {
        $membersValidationElement = $this->getElement('ui_segment')->find('css', '.sylius-validation-error');
        if (null === $membersValidationElement) {
            throw new ElementNotFoundException($this->getDriver(), 'members validation box', 'css', '.sylius-validation-error');
        }

        return $membersValidationElement->getText() === $message;
    }

    /**
     * {@inheritdoc}
     */
    public function chooseMember($name)
    {
        $selectItems = $this->getDocument()->waitFor(2, function () {
            return $this->getDocument()->findAll('css', 'div[data-form-type="collection"] select');
        });
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"] select');
        }

        $lastSelectItem->selectOption($name);
    }

    /**
     * {@inheritdoc}
     */
    public function selectScope($scope)
    {
        $this->getDocument()->selectFieldOption('Scope', $scope);
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        $typeField = $this->getElement('type');
        $selectedOption = $typeField->find('css', 'option[selected]');

        return lcfirst($selectedOption->getText()) === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeFieldDisabled()
    {
        return $this->getElement('type')->hasAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_zone_code',
            'name' => '#sylius_zone_name',
            'type' => '#sylius_zone_type',
            'ui_segment' => '.ui.segment',
        ]);
    }
}
