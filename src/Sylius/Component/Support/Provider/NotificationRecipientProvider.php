<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\Component\Support\Provider;

use Sylius\Component\Support\Adapter\ConfigNotificationRecipientAdapterInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class NotificationRecipientProvider implements NotificationRecipientProviderInterface
{
    /**
     * @var ConfigNotificationRecipientAdapterInterface
     */
    private $emailAdapter;

    /**
     * @param ConfigNotificationRecipientAdapterInterface $emailAdapter
     */
    public function __construct(ConfigNotificationRecipientAdapterInterface $emailAdapter)
    {
        $this->emailAdapter = $emailAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmails()
    {
        return $this->emailAdapter->getEmails();
    }
}
