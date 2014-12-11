<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Provider;

use Sylius\Component\Sequence\Model\SequenceInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SequenceProviderInterface
{
    /**
     * Get the sequence for the specified type
     *
     * @param  string            $type
     * @return SequenceInterface
     */
    public function getSequence($type);
}
