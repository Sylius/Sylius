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

namespace Sylius\Bundle\CoreBundle\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Templating\FilterExtension as BaseFilterExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @psalm-suppress DeprecatedClass
 */
final class FilterExtension extends BaseFilterExtension
{
    private string $imagesPath;

    public function __construct(string $imagesPath, CacheManager $cache)
    {
        $this->imagesPath = $imagesPath;

        /** @psalm-suppress DeprecatedClass */
        parent::__construct($cache);
    }

    public function filter(
        $path,
        $filter,
        array $config = [],
        $resolver = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_URL
    ) {
        if (!$this->canImageBeFiltered($path)) {
            return $this->imagesPath . $path;
        }

        /** @psalm-suppress DeprecatedClass */
        return parent::filter($path, $filter, $config, $resolver, $referenceType);
    }

    private function canImageBeFiltered(string $path): bool
    {
        return substr($path, -4) !== '.svg';
    }
}
