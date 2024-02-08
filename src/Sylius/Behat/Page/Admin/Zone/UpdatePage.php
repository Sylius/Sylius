<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Zone;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use ChecksCodeImmutability;

    public function countMembers(): int
    {
        $selectedZoneMembers = $this->getSelectedZoneMembers();

        return count($selectedZoneMembers);
    }

    public function getScope(): string
    {
        return $this->getElement('scope')->getValue();
    }

    public function hasMember(ZoneMemberInterface $zoneMember): bool
    {
        $selectedZoneMembers = $this->getSelectedZoneMembers();

        foreach ($selectedZoneMembers as $selectedZoneMember) {
            if ($selectedZoneMember->getValue() === $zoneMember->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function removeMember(ZoneMemberInterface $zoneMember): void
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
     * @throws ElementNotFoundException
     */
    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
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
     * @throws ElementNotFoundException
     */
    private function getDeleteButtonForCollectionItem(NodeElement $item): NodeElement
    {
        $deleteButton = $item->find('css', 'a[data-form-collection="delete"]');
        if (null === $deleteButton) {
            throw new ElementNotFoundException($this->getDriver(), 'link', 'css', 'a[data-form-collection="delete"]');
        }

        return $deleteButton;
    }

    /**
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    private function getSelectedZoneMembers(): array
    {
        $zoneMembers = $this->getElement('zone_members');

        return $zoneMembers->findAll('css', 'option[selected="selected"]');
    }
}
