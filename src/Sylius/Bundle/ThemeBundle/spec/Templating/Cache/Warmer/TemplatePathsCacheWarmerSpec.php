<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Cache\Warmer;

use Doctrine\Common\Cache\Cache;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Templating\Cache\Warmer\TemplatePathsCacheWarmer;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocatorInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplatePathsCacheWarmerSpec extends ObjectBehavior
{
    function let(
        TemplateFinderInterface $templateFinder,
        TemplateLocatorInterface $templateLocator,
        ThemeRepositoryInterface $themeRepository,
        Cache $cache
    ) {
        $this->beConstructedWith($templateFinder, $templateLocator, $themeRepository, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TemplatePathsCacheWarmer::class);
    }

    function it_implements_cache_warmer_interface()
    {
        $this->shouldImplement(CacheWarmerInterface::class);
    }

    function it_builds_cache_by_warming_up_every_template_and_every_theme_together(
        TemplateFinderInterface $templateFinder,
        TemplateLocatorInterface $templateLocator,
        ThemeRepositoryInterface $themeRepository,
        Cache $cache,
        ThemeInterface $theme,
        TemplateReferenceInterface $firstTemplate,
        TemplateReferenceInterface $secondTemplate
    ) {
        $templateFinder->findAllTemplates()->willReturn([$firstTemplate, $secondTemplate]);

        $themeRepository->findAll()->willReturn([$theme]);

        $theme->getName()->willReturn('theme/name');
        $firstTemplate->getLogicalName()->willReturn('Logical:Name:First');
        $secondTemplate->getLogicalName()->willReturn('Logical:Name:Second');

        $templateLocator->locateTemplate($firstTemplate, $theme)->willReturn('/First/Theme/index.html.twig');
        $templateLocator->locateTemplate($secondTemplate, $theme)->willThrow(ResourceNotFoundException::class);

        $cache->save('Logical:Name:First|theme/name', '/First/Theme/index.html.twig')->shouldBeCalled();
        $cache->save('Logical:Name:Second|theme/name', null)->shouldBeCalled();

        $this->warmUp(null);
    }
}
