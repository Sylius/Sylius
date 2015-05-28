<?php

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Exception\InvalidArgumentException;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Symfony\Component\Config\Loader\Loader;

/**
 * Abstract loader for themes based on files.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class ThemeLoader extends Loader
{
    /**
     * @var ThemeFactoryInterface
     */
    private $themeFactory;

    /**
     * @param ThemeFactoryInterface $themeFactory
     */
    public function __construct(ThemeFactoryInterface $themeFactory)
    {
        $this->themeFactory = $themeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (!file_exists($resource)) {
            throw new InvalidArgumentException(sprintf('Given theme metadata file "%s" does not exists!', $resource));
        }

        $themeData = $this->transformResourceContentsToArray(file_get_contents($resource));

        $theme = $this->themeFactory->createFromArray($themeData);
        $theme->setPath(realpath(dirname($resource)));

        return $theme;
    }

    /**
     * Returns theme data array from resource contents.
     *
     * @param string $contents
     *
     * @return array
     */
    abstract protected function transformResourceContentsToArray($contents);
}