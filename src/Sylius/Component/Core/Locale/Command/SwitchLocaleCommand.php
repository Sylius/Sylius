<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale\Command;

use SimpleBus\Message\Name\NamedMessage;
use Sylius\Component\Core\Locale\ValueObject\LocaleCode;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SwitchLocaleCommand implements NamedMessage
{
    /**
     * @var LocaleCode
     */
    private $localeCode;

    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * @param LocaleCode $localeCode
     * @param ChannelInterface $channel
     */
    public function __construct(LocaleCode $localeCode, ChannelInterface $channel)
    {
        $this->localeCode = $localeCode;
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function LocaleCode()
    {
        return (string) $this->localeCode;
    }

    /**
     * @return ChannelInterface
     */
    public function channel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public static function messageName()
    {
        return 'sylius.command.switch_locale';
    }
}
