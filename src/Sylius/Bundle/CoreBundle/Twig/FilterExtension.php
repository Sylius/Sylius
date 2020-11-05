<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Templating\FilterExtension as BaseFilterExtension;

final class FilterExtension extends BaseFilterExtension
{
    /** @var string */
    private $imagesPath;

    public function __construct(string $imagesPath, CacheManager $cache)
    {
        $this->imagesPath = $imagesPath;

        parent::__construct($cache);
    }

    public function filter($path, $filter, array $config = [], $resolver = null)
    {
        if (!$this->canImageBeFiltered($path)) {
            return $this->imagesPath.$path;
        }

        return parent::filter($path, $filter, $config, $resolver);
    }

    private function canImageBeFiltered(string $path): bool
    {
        return substr($path, -3) !== 'svg';
    }
}
