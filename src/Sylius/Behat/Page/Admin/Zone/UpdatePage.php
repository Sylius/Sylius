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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt, ChecksCodeImmutability;

    /**
     * @var array
     */
    protected $elements = [
        'code' => '#sylius_zone_code',
        'name' => '#sylius_zone_name',
        'type' => '#sylius_zone_type',
        'member' => '.one.field',
        'zone_members' => '#sylius_zone_members',
    ];

    /**
     * {@inheritdoc}
     */
    public function countMembers()
    {
        try {
            $selectedZoneMembers = $this->getSelectedZoneMembers();

            return count($selectedZoneMembers);
        } catch (ElementNotFoundException $exception) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $zoneMember)
    {
        try {
            $selectedZoneMembers = $this->getSelectedZoneMembers();

            foreach ($selectedZoneMembers as $selectedZoneMember) {
                if ($selectedZoneMember->getValue() === $zoneMember->getCode()) {
                    return true;
                }
            }

            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
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
                throw new \RuntimeException('Cannot find selected option');
            }

            if ($selectedItem->getValue() === $zoneMember->getCode()) {
                $deleteButton = $item->find('css', 'a[data-form-collection="delete"]');

                if (null === $deleteButton) {
                    throw new \RuntimeException('Cannot find delete button');
                }
                $deleteButton->click();

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
