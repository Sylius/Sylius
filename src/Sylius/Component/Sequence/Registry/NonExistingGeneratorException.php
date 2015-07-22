<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Registry;

use Sylius\Component\Registry\NonExistingServiceException;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NonExistingGeneratorException extends \InvalidArgumentException
{
    public function __construct($entity, NonExistingServiceException $e)
    {
        parent::__construct(
            sprintf(
                'Generator for entity "%s" does not exist. Please consider adding it to your configuration like this: sylius_sequence.generators: %%sylius.model.class%%: sylius.sequence.sequential_number_generator',
                get_class($entity)
            ),
            $e->getCode(),
            $e
        );
    }
}
