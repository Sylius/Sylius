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

use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt, SpecifiesItsCode;

    protected $elements = [
        'lastAddedMemberList' => null,
    ];

    /**
     * @var int
     */
    private $memberCounter = 0;

    public function addMember()
    {
        $this->getDocument()->clickLink('Add member');
        $this->elements['lastAddedMemberList'] = sprintf('#sylius_zone_members_%s_code', $this->memberCounter);
        $this->memberCounter++;
    }

    /**
     * {@inheritdoc}
     */
    public function chooseMember($name)
    {
        if (null === $this->elements['lastAddedMemberList']) {
            throw new \RuntimeException('You need to add new member!');
        }

        $this->getElement('lastAddedMemberList')->selectOption($name);
    }
}
