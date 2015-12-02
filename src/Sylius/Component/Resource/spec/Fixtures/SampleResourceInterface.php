<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Fixtures;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface SampleResourceInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getRating();

    /**
     * @return string
     */
    public function getTitle();
}
