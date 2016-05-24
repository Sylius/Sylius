<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Uploader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class NonrandomPathGenerator implements PathGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(\SplFileInfo $file)
    {
        for ($i = 0; true; ++$i) {
            yield md5(sprintf('%s.%s.%d', $file->getFilename(), $file->getMTime(), $i)) . '.' . $file->getExtension();
        }
    }
}
