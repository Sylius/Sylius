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

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** @internal */
final class LegacySonataBlockExtension extends AbstractExtension
{
    /** @var array */
    private $whitelistedVariables;

    public function __construct(array $whitelistedVariables)
    {
        $this->whitelistedVariables = $whitelistedVariables;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sonata_block_whitelisted_variables', [$this, 'getWhitelistedVariables']),
        ];
    }

    public function getWhitelistedVariables(): array
    {
        return $this->whitelistedVariables;
    }
}
