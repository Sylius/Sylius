<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Finder;

use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslationFilesFinder implements TranslationFilesFinderInterface
{
    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function findTranslationFiles($path)
    {
        $themeFiles = $this->getFiles($path);

        $translationsFiles = [];
        foreach ($themeFiles as $themeFile) {
            if (!$this->isTranslationFile($themeFile)) {
                continue;
            }

            $translationsFiles[] = (string) $themeFile;
        }

        return $translationsFiles;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private function getFiles($path)
    {
        $finder = $this->finderFactory->create();

        $finder
            ->ignoreUnreadableDirs()
            ->in($path)
        ;

        return $finder;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function isTranslationFile($file)
    {
        return false !== strpos($file, 'translations/')
            && (bool) preg_match('/^[^\.]+?\.[a-zA-Z_]{2,}?\.[a-z0-9]{2,}?$/', basename($file));
    }
}
