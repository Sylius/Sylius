<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\spec\Fixtures;

interface SampleInterface
{
    /**
     * @return int
     */
    public function getInt();

    /**
     * @return string
     */
    public function getString();

    /**
     * @return mixed
     */
    public function getBizarrelyNamedProperty();

    /**
     * @return SampleInterface
     */
    public function getInnerSample();
}
