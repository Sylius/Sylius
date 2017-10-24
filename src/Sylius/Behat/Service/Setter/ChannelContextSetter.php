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

namespace Sylius\Behat\Service\Setter;

use Sylius\Component\Channel\Model\ChannelInterface;

final class ChannelContextSetter implements ChannelContextSetterInterface
{
    /**
     * @var CookieSetterInterface
     */
    private $cookieSetter;

    /**
     * @param CookieSetterInterface $cookieSetter
     */
    public function __construct(CookieSetterInterface $cookieSetter)
    {
        $this->cookieSetter = $cookieSetter;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel(ChannelInterface $channel)
    {
        $this->cookieSetter->setCookie('_channel_code', $channel->getCode());
    }
}
