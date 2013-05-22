<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use SplFileInfo;

interface ImageTaxonInterface extends ImageInterface
{
    public function hasImageFile();
    public function getImageFile();
    public function setImageFile(SplFileInfo $file);
    public function getImagePath();
    public function setImagePath($path);
}