<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EmailManager;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ContactEmailManagerInterface
{
    /**
     * @param array $data
     * @param array $recipients
     */
    public function sendContactRequest(array $data, array $recipients);
}
