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

final class OrderingTranslationFilesFinder implements TranslationFilesFinderInterface
{
    /**
     * @var TranslationFilesFinderInterface
     */
    private $translationFilesFinder;

    /**
     * @param TranslationFilesFinderInterface $translationFilesFinder
     */
    public function __construct(TranslationFilesFinderInterface $translationFilesFinder)
    {
        $this->translationFilesFinder = $translationFilesFinder;
    }

    public function findTranslationFiles(string $path): array
    {
        $files = $this->translationFilesFinder->findTranslationFiles($path);

        usort($files, function (string $firstFile, string $secondFile) use ($path): int {
            $firstFile = str_replace($path, '', $firstFile);
            $secondFile = str_replace($path, '', $secondFile);

            return strpos($secondFile, 'translations') - strpos($firstFile, 'translations');
        });

        return $files;
    }
}
