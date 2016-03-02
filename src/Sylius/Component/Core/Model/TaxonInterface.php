<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Media\Model\ImageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as VariableTaxonInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonInterface extends VariableTaxonInterface
{
    /**
     * @return ImageInterface
     */
    public function getImage();

    /**
     * @param ImageInterface $image
     */
    public function setImage(ImageInterface $image);

    /**
     * @return bool
     */
    public function hasImage();

    /**
     * @return Collection|ProductInterface[]
     */
    public function getProducts();

    /**
     * @param ProductInterface[] $products
     */
    public function setProducts($products);
}
