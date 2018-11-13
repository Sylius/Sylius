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

namespace Sylius\Behat\Service\Checker;

use Liip\ImagineBundle\Service\FilterService;

final class ImageExistenceChecker implements ImageExistenceCheckerInterface
{
    /** @var FilterService */
    private $filterService;

    /** @var string */
    private $mediaRootPath;

    public function __construct(FilterService $filterService, string $mediaRootPath)
    {
        $this->filterService = $filterService;
        $this->mediaRootPath = $mediaRootPath;
    }

    public function doesImageWithUrlExist(string $imageUrl, string $liipImagineFilter): bool
    {
        $imageUrl = str_replace($liipImagineFilter . '/', '', substr($imageUrl, strpos($imageUrl, $liipImagineFilter), strlen($imageUrl)));

        $browserImagePath = $this->filterService->getUrlOfFilteredImage($imageUrl, $liipImagineFilter);

        return file_exists($this->mediaRootPath . parse_url($browserImagePath, \PHP_URL_PATH));
    }
}
