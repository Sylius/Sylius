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

namespace Sylius\Bundle\ApiBundle\Application\Entity;

use Sylius\Component\Currency\Model\Currency as BaseCurrency;

class Currency extends BaseCurrency
{
    private ?string $type = null;

    public function __construct()
    {
        parent::__construct();

        $this->type = 'default';
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
