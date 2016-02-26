<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Channel;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelCreatePage extends SymfonyPage
{
    /**
     * @param string $name
     */
    public function fillName($name)
    {
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * @param string $code
     */
    public function fillCode($code)
    {
        $this->getDocument()->fillField('Code', $code);
    }

    public function create()
    {
        $this->getDocument()->pressButton('Create');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_backend_channel_create';
    }
}
