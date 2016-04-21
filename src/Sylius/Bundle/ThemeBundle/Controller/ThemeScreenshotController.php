<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Controller;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotController
{
    /**
     * @var RepositoryInterface
     */
    private $themeRepository;

    /**
     * @param RepositoryInterface $themeRepository
     */
    public function __construct(RepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param int $themeId
     * @param int $screenshotNumber
     *
     * @return BinaryFileResponse
     */
    public function streamScreenshotAction($themeId, $screenshotNumber)
    {
        $screenshotPath = $this->getScreenshotPath($this->getTheme($themeId), $screenshotNumber);

        try {
            return new BinaryFileResponse($screenshotPath);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException(sprintf('Screenshot "%s" does not exist', $screenshotPath), $exception);
        }
    }

    /**
     * @param ThemeInterface $theme
     * @param int $screenshotNumber
     *
     * @return string
     */
    private function getScreenshotPath(ThemeInterface $theme, $screenshotNumber)
    {
        $screenshots = $theme->getScreenshots();

        if (!isset($screenshots[$screenshotNumber])) {
            throw new NotFoundHttpException(sprintf('Theme "%s" does not have screenshot #%d', $theme->getTitle(), $screenshotNumber));
        }

        $screenshotRelativePath = $screenshots[$screenshotNumber];

        return rtrim($theme->getPath(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR . $screenshotRelativePath;
    }

    /**
     * @param int $themeId
     *
     * @return ThemeInterface
     */
    private function getTheme($themeId)
    {
        $theme = $this->themeRepository->find($themeId);
        if (null === $theme) {
            throw new NotFoundHttpException(sprintf('Theme with id %d not found', $themeId));
        }

        return $theme;
    }
}
