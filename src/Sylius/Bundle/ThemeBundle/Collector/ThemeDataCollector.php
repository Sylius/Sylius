<?php

namespace Sylius\Bundle\ThemeBundle\Collector;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeDataCollector implements DataCollectorInterface, \Serializable
{
    /**
     * @var ThemeInterface[]
     */
    private $usedThemes;

    /**
     * @var ThemeInterface[]
     */
    private $allThemes;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var integer[]
     */
    private $themesPriorities;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface $themeContext
     */
    public function __construct(ThemeRepositoryInterface $themeRepository, ThemeContextInterface $themeContext)
    {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    /**
     * @return ThemeInterface[]
     */
    public function getUsedThemes()
    {
        return $this->usedThemes;
    }

    /**
     * @return ThemeInterface[]
     */
    public function getAllThemes()
    {
        return $this->allThemes;
    }

    /**
     * @return integer[]
     */
    public function getThemesPriorities()
    {
        return $this->themesPriorities;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (null !== $this->themeContext) {
            $this->usedThemes = $this->themeContext->getThemesSortedByPriorityInDescendingOrder();
            $this->themesPriorities = $this->themeContext->getThemesPriorities();
        }

        if (null !== $this->themeRepository) {
            $this->allThemes = $this->themeRepository->findAll();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->usedThemes, $this->allThemes, $this->themesPriorities]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->usedThemes, $this->allThemes, $this->themesPriorities) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_theme';
    }
}