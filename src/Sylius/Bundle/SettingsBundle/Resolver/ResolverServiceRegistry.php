<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistry;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ResolverServiceRegistry extends ServiceRegistry
{
    /**
     * @var SettingsResolverInterface
     */
    protected $defaultResolver;

    /**
     * @param string $interface
     * @param SettingsResolverInterface $defaultResolver
     */
    public function __construct($interface, SettingsResolverInterface $defaultResolver)
    {
        parent::__construct($interface, 'Settings resolver');

        $this->defaultResolver = $defaultResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function get($type)
    {
        try {
            return parent::get($type);
        } catch (NonExistingServiceException $e) {
            return $this->defaultResolver;
        }
    }
}
