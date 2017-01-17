<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Collector;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeCollector extends DataCollector
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface $themeContext
     * @param ThemeHierarchyProviderInterface $themeHierarchyProvider
     */
    public function __construct(
        ThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext,
        ThemeHierarchyProviderInterface $themeHierarchyProvider
    ) {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
    }

    /**
     * @return ThemeInterface
     */
    public function getUsedTheme()
    {
        return $this->data['used_theme'];
    }

    /**
     * @return ThemeInterface[]
     */
    public function getUsedThemes()
    {
        return $this->data['used_themes'];
    }

    /**
     * @return ThemeInterface[]
     */
    public function getThemes()
    {
        return $this->data['themes'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['used_theme'] = $this->themeContext->getTheme();
        $this->data['used_themes'] = $this->themeHierarchyProvider->getThemeHierarchy($this->themeContext->getTheme());
        $this->data['themes'] = $this->themeRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_theme';
    }
}
