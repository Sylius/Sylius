<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @internal
 * @experimental
 */
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
