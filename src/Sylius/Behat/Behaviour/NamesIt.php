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

namespace Sylius\Behat\Behaviour;

trait NamesIt
{
    use DocumentAccessor;

    /**
     * @param string $name
     */
    public function nameIt($name)
    {
        $this->getDocument()->fillField('Name', $name);
    }
}
