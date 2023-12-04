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

namespace Sylius\Bundle\CoreBundle\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Templating\FilterExtension as BaseFilterExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class FilterExtension extends BaseFilterExtension
{
    public function __construct(private string $imagesPath, CacheManager $cache)
    {
        parent::__construct($cache);
    }

    public function filter(
        $path,
        $filter,
        array $config = [],
        $resolver = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_URL,
    ) {
        if (!$this->canImageBeFiltered($path)) {
            return $this->imagesPath . $path;
        }

        return parent::filter($path, $filter, $config, $resolver, $referenceType);
    }

    private function canImageBeFiltered(string $path): bool
    {
        return !str_ends_with($path, '.svg');
    }
}
