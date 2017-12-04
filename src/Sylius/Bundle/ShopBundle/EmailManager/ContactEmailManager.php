<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ContactEmailManager implements ContactEmailManagerInterface
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * {@inheritdoc}
     */
    public function sendContactRequest(array $data, array $recipients): void
    {
        $this->emailSender->send(Emails::CONTACT_REQUEST, $recipients, ['data' => $data], [], [$data['email']]);
    }
}
