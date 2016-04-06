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

            /** @var NodeElement $selectedZoneMember */
            foreach ($selectedZoneMembers as $selectedZoneMember) {
                $isMatched = $selectedZoneMember->getAttribute('data-value') === $zoneMember->getCode();

                if (true === $isMatched) {
                    return $isMatched;
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
        $deleteButtons = $zoneMembers->findAll('css', 'a[data-form-collection="delete"]');

        /** @var NodeElement $deleteButton */
        foreach ($deleteButtons as $deleteButton) {
            $parent = $deleteButton->getParent()->getParent();
            $active = $parent->find('css', '.item.active.selected');

            if ($active->getAttribute('data-value') === $zoneMember->getCode()) {
                break;
            }
        }

        $deleteButton->press();
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
        $selectedZoneMembers = $zoneMembers->findAll('css', '.item.active.selected');

        return $selectedZoneMembers;
    }
}
