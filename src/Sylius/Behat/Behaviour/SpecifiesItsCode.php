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

trait SpecifiesItsCode
{
    use DocumentAccessor;

    /**
     * @param string $code
     */
    public function specifyCode($code)
    {
        $this->getDocument()->fillField('Code', $code);
    }
}
