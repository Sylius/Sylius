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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'code' => '#sylius_zone_code',
        'name' => '#sylius_zone_name',
        'type' => '#sylius_zone_type',
        'current_item_member_code' => '#sylius_zone_members_0_code',
    ];

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $zoneMember)
    {
        try {
            $element = $this->getElement('current_item_member_code');
            $selectedElement = $element->find('css', 'option[selected="selected"]');

            return $selectedElement->getAttribute('value') === $zoneMember->getCode();
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }
}
