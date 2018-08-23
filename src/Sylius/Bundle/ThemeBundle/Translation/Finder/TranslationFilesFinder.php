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

namespace Sylius\Bundle\ThemeBundle\Translation\Finder;

use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Symfony\Component\Finder\SplFileInfo;

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
    public function findTranslationFiles(string $path): array
    {
        $themeFiles = $this->getFiles($path);

        $translationsFiles = [];
        foreach ($themeFiles as $themeFile) {
            $themeFilepath = (string) $themeFile;

            if (!$this->isTranslationFile($themeFilepath)) {
                continue;
            }

            $translationsFiles[] = $themeFilepath;
        }

        return $translationsFiles;
    }

    /**
     * @param string $path
     *
     * @return iterable|SplFileInfo[]
     */
    private function getFiles(string $path): iterable
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
    private function isTranslationFile(string $file): bool
    {
        return false !== strpos($file, 'translations' . DIRECTORY_SEPARATOR)
            && (bool) preg_match('/^[^\.]+?\.[a-zA-Z_]{2,}?\.[a-z0-9]{2,}?$/', basename($file));
    }
}
