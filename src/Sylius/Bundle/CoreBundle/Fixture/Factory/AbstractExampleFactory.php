<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
abstract class AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    abstract protected function configureOptions(OptionsResolver $resolver);
}
