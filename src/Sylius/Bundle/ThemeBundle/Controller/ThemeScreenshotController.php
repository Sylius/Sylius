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
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotController
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param string $themeName
     * @param int $screenshotNumber
     *
     * @return BinaryFileResponse
     */
    public function streamScreenshotAction($themeName, $screenshotNumber)
    {
        $screenshotPath = $this->getScreenshotPath($this->getTheme($themeName), $screenshotNumber);

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
     * @param string $themeName
     *
     * @return ThemeInterface
     */
    private function getTheme($themeName)
    {
        $theme = $this->themeRepository->findOneByName($themeName);
        if (null === $theme) {
            throw new NotFoundHttpException(sprintf('Theme with name "%s" not found', $themeName));
        }

        return $theme;
    }
}
