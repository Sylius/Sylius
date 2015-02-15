<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Sender;

use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

/**
 * Basic sender, which uses adapters system.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Sender implements SenderInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var EmailProviderInterface
     */
    protected $provider;

    /**
     * @param AdapterInterface $adapter
     * @param EmailProviderInterface $provider
     */
    public function __construct(AdapterInterface $adapter, EmailProviderInterface $provider)
    {
        $this->adapter = $adapter;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function send($code, array $recipients, array $data = array())
    {
        $email = $this->provider->getEmail($code);

        if (!$email->isEnabled()) {
            return;
        }

        $this->adapter->send($email, $recipients, $data);
    }
}
