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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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

    public function findTranslationFiles(ThemeInterface $theme)
    {
        $files = $this->translationFilesFinder->findTranslationFiles($theme);

        /*
         * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
         * "usort(): Array was modified by the user comparison function"
         */
        @usort($files, function ($firstFile, $secondFile) use ($theme) {
            $firstFile = str_replace($theme->getPath(), '', $firstFile);
            $secondFile = str_replace($theme->getPath(), '', $secondFile);

            return strpos($secondFile, 'translations') - strpos($firstFile, 'translations');
        });

        return $files;
    }
}
