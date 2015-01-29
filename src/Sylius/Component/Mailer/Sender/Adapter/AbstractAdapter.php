<?php

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
