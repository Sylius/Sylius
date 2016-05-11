<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\SymfonyPage;
use Webmozart\Assert\Assert;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isRegistered()
    {
        $username = $this->getDocument()->find('css', '#username')->getText();

        return '' !== $username;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAccount()
    {
        $deleteButton = $this->getElement('delete account button');
        $deleteButton->press();

        $confirmationModal = $this->getDocument()->find('css', '#confirmation-modal-confirm');
        $this->waitForModalToAppear($confirmationModal);
        $confirmationModal->find('css', 'a:contains("Delete")')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_backend_customer_show';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'delete account button' => '.delete-action-form',
        ]);
    }
}
