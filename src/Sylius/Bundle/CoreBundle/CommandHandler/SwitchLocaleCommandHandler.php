<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\CommandHandler;

use Sylius\Component\Core\Locale\Command\SwitchLocaleCommand;
use Sylius\Component\Core\Locale\LocaleStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SwitchLocaleCommandHandler
{
    /**
     * @var LocaleStorageInterface
     */
    private $localeStorage;

    /**
     * @param LocaleStorageInterface $localeStorage
     */
    public function __construct(LocaleStorageInterface $localeStorage)
    {
        $this->localeStorage = $localeStorage;
    }

    /**
     * @param SwitchLocaleCommand $command
     */
    public function handle(SwitchLocaleCommand $command)
    {
        $this->localeStorage->set($command->channel(), $command->LocaleCode());
    }
}
