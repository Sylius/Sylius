<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Test;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TestThemeConfigurationManager implements TestThemeConfigurationManagerInterface
{
    /**
     * @var ConfigurationProcessorInterface
     */
    private $configurationProcessor;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $configurationsFile;

    /**
     * @param ConfigurationProcessorInterface $configurationProcessor
     * @param string $cacheDir
     */
    public function __construct(ConfigurationProcessorInterface $configurationProcessor, $cacheDir)
    {
        $this->configurationProcessor = $configurationProcessor;
        $this->filesystem = new Filesystem();
        $this->configurationsFile = rtrim($cacheDir, '/') . '/_test_themes/data.serialized';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $this->initializeIfNeeded();

        return $this->load();
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $configuration)
    {
        $this->initializeIfNeeded();

        $configuration = $this->configurationProcessor->process([$configuration]);
        $configuration['path'] = $this->getThemeDirectory($configuration['name']);

        $this->initializeTheme($configuration['name']);

        $configurations = $this->load();
        $configurations[] = $configuration;
        $this->save($configurations);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($themeName)
    {
        $this->initializeIfNeeded();

        $this->clearTheme($themeName);

        $configurations = $this->load();
        $configurations = array_filter($configurations, function ($configuration) use ($themeName) {
            return isset($configuration['name']) && $configuration['name'] !== $themeName;
        });
        $this->save($configurations);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $configurationsDirectory = dirname($this->configurationsFile);

        if ($this->filesystem->exists($configurationsDirectory)) {
            $this->filesystem->remove($configurationsDirectory);
        }
    }

    /**
     * @return array
     */
    private function load()
    {
        return unserialize(file_get_contents($this->configurationsFile));
    }

    /**
     * @param array $configurations
     */
    private function save(array $configurations)
    {
        file_put_contents($this->configurationsFile, serialize($configurations));
    }

    private function initializeIfNeeded()
    {
        if ($this->filesystem->exists($this->configurationsFile)) {
            return;
        }

        $this->initialize();
    }

    private function initialize()
    {
        $configurationsDirectory = dirname($this->configurationsFile);

        $this->filesystem->mkdir($configurationsDirectory);

        $this->save([]);
    }

    /**
     * @param string $themeName
     */
    private function initializeTheme($themeName)
    {
        $themeDirectory = $this->getThemeDirectory($themeName);

        $this->filesystem->mkdir($themeDirectory);
    }

    /**
     * @param string $themeName
     */
    private function clearTheme($themeName)
    {
        $themeDirectory = $this->getThemeDirectory($themeName);

        if (!$this->filesystem->exists($themeDirectory)) {
            return;
        }

        $this->filesystem->remove($themeDirectory);
    }

    /**
     * @param string $themeName
     *
     * @return string
     */
    private function getThemeDirectory($themeName)
    {
        return rtrim(dirname($this->configurationsFile), '/') . '/' . $themeName;
    }
}
