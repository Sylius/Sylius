<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Contact;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ContactPageInterface extends PageInterface
{
    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $message
     */
    public function specifyMessage($message);

    public function send();

    /**
     * @param string $element
     *
     * @return string
     */
    public function getValidationMessageFor($element);
}
