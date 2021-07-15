<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Applicator;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductImage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
final class ProductImageFilterController
{
    private CacheManager $cache;
    private RequestStack $requestStack;

    public function __construct(CacheManager $cache, RequestStack $requestStack)
    {
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    public function __invoke(ProductImage $data): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $path = $this->cache->getBrowserPath(parse_url($data->getPath(), PHP_URL_PATH), $request->query->get('filter'));

        return new RedirectResponse($path);
    }
}
