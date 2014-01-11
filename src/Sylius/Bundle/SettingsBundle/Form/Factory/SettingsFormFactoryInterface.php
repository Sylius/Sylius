<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Form\Factory;

use Symfony\Component\Form\FormInterface;

/**
 * Settings form factory interface
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SettingsFormFactoryInterface
{
    /**
     * Create the form for given schema.
     *
     * @param string $namespace
     *
     * @return FormInterface
     */
    public function create($namespace);
}
