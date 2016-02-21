<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
abstract class AbstractSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureContext(OptionsResolver $resolver)
    {
    }
}
