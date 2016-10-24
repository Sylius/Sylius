<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Controller\ThemeScreenshotController;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotControllerSpec extends ObjectBehavior
{
    /**
     * @var string
     */
    private $fixturesPath;

    function let(ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($themeRepository);

        $this->fixturesPath = realpath(__DIR__ . '/../Fixtures');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeScreenshotController::class);
    }

    function it_streams_screenshot_as_a_response(ThemeRepositoryInterface $themeRepository, ThemeInterface $theme)
    {
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $theme->getScreenshots()->willReturn([
            'screenshot/0-amazing.jpg', // exists
            'screenshot/1-awesome.jpg', // does not exist
        ]);
        $theme->getPath()->willReturn($this->fixturesPath);

        $this
            ->streamScreenshotAction('theme/name', 0)
            ->shouldBeBinaryFileResponseStreamingFile($this->fixturesPath . '/screenshot/0-amazing.jpg')
        ;
    }

    function it_throws_not_found_http_exception_if_screenshot_cannot_be_found(
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ) {
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $theme->getScreenshots()->willReturn([
            'screenshot/0-amazing.jpg', // exists
            'screenshot/1-awesome.jpg', // does not exists
        ]);
        $theme->getPath()->willReturn($this->fixturesPath);

        $this
            ->shouldThrow(new NotFoundHttpException(sprintf(
                'Screenshot "%s/screenshot/1-awesome.jpg" does not exist',
                $this->fixturesPath
            )))
            ->during('streamScreenshotAction', ['theme/name', 1])
        ;
    }

    function it_throws_not_found_http_exception_if_screenshot_number_exceeds_the_number_of_theme_screenshots(
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ) {
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $theme->getScreenshots()->willReturn([
            'screenshot/0-amazing.jpg',
            'screenshot/1-awesome.jpg',
        ]);
        $theme->getTitle()->willReturn('Candy shop');

        $this
            ->shouldThrow(new NotFoundHttpException('Theme "Candy shop" does not have screenshot #4'))
            ->during('streamScreenshotAction', ['theme/name', 4])
        ;
    }

    function it_throws_not_found_http_exception_if_theme_with_given_id_cannot_be_found(ThemeRepositoryInterface $themeRepository)
    {
        $themeRepository->findOneByName('theme/name')->willReturn(null);

        $this
            ->shouldThrow(new NotFoundHttpException('Theme with name "theme/name" not found'))
            ->during('streamScreenshotAction', ['theme/name', 666])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beBinaryFileResponseStreamingFile' => function (BinaryFileResponse $response, $file) {
                return $response->getFile()->getRealPath() === $file;
            },
        ];
    }
}
