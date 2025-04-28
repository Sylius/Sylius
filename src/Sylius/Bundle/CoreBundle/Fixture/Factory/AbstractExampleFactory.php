<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ResourceInterface> */
abstract class AbstractExampleFactory implements ExampleFactoryInterface
{
    abstract protected function configureOptions(OptionsResolver $resolver): void;
}
