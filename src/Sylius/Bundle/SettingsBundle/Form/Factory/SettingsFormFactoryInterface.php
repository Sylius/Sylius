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
 * Settings form factory interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsFormFactoryInterface
{
    /**
     * Create the form for given schema.
     *
     * @param string     $namespace
     * @param null|mixed $data
     * @param array      $options
     *
     * @return FormInterface
     */
    public function create($namespace, $data = null, array $options = array());
}
