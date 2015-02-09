<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Sender\Adapter;

use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractAdapter
{
    /**
     * @var DefaultSettingsProviderInterface
     */
    protected $defaultSettingsProvider;

    /**
     * @param DefaultSettingsProviderInterface $defaultSettingsProvider
     */
    public function __construct(DefaultSettingsProviderInterface $defaultSettingsProvider)
    {
        $this->defaultSettingsProvider = $defaultSettingsProvider;
    }
}
