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

namespace Sylius\Component\Promotion\DTO;

final class CatalogPromotion
{
    public string $name;

    public string $code;

    public array $rules;

    public function __construct(string $name, string $code, array $rules)
    {
        $this->name = $name;
        $this->code = $code;
        $this->rules = $rules;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
