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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function countMembers()
    {
        $selectedZoneMembers = $this->getSelectedZoneMembers();

        return count($selectedZoneMembers);
    }

    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        return $this->getElement('scope')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $zoneMember)
    {
        $selectedZoneMembers = $this->getSelectedZoneMembers();

        foreach ($selectedZoneMembers as $selectedZoneMember) {
            if ($selectedZoneMember->getValue() === $zoneMember->getCode()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMember(ZoneMemberInterface $zoneMember)
    {
        $zoneMembers = $this->getElement('zone_members');
        $items = $zoneMembers->findAll('css', 'div[data-form-collection="item"]');

        /** @var NodeElement $item */
        foreach ($items as $item) {
            $selectedItem = $item->find('css', 'option[selected="selected"]');

            if (null === $selectedItem) {
                throw new ElementNotFoundException($this->getDriver(), 'selected option', 'css', 'option[selected="selected"]');
            }

            if ($selectedItem->getValue() === $zoneMember->getCode()) {
                $this->getDeleteButtonForCollectionItem($item)->click();

                break;
            }
        }
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_zone_code',
            'member' => '.one.field',
            'name' => '#sylius_zone_name',
            'scope' => '#sylius_zone_scope',
            'type' => '#sylius_zone_type',
            'zone_members' => '#sylius_zone_members',
        ]);
    }

    /**
     * @param NodeElement $item
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getDeleteButtonForCollectionItem(NodeElement $item)
    {
        $deleteButton = $item->find('css', 'a[data-form-collection="delete"]');
        if (null === $deleteButton) {
            throw new ElementNotFoundException($this->getDriver(), 'link', 'css', 'a[data-form-collection="delete"]');
        }

        return $deleteButton;
    }

    /**
     * @return \Behat\Mink\Element\NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    private function getSelectedZoneMembers()
    {
        $zoneMembers = $this->getElement('zone_members');
        $selectedZoneMembers = $zoneMembers->findAll('css', 'option[selected="selected"]');

        return $selectedZoneMembers;
    }
}
