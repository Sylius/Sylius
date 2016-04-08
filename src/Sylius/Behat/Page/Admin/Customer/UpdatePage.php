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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'email' => '#sylius_customer_email',
        'first name' => '#sylius_customer_firstName',
        'last name' => '#sylius_customer_lastName',
    ];

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        $firstNameElement = $this->getElement('first name')->getValue();
        $lastNameElement = $this->getElement('last name')->getValue();

        return sprintf('%s %s', $firstNameElement, $lastNameElement);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailHasValue($elementValue)
    {
        $element = $this->getElement('email');

        return $elementValue === $element->getValue();
    }
}
