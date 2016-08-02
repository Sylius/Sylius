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
use Sylius\Component\Taxonomy\Model\TaxonInterface as VariableTaxonInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonInterface extends VariableTaxonInterface
{
    /**
     * @return Collection|ProductInterface[]
     */
    public function getProducts();

    /**
     * @param ProductInterface[] $products
     */
    public function setProducts($products);

    /**
     * @return bool
     */
    public function hasImages();
    
    /**
     * @return Collection|TaxonImageInterface[]
     */
    public function getImages();

    /**
     * @param $code
     * @return TaxonImageInterface|null;
     */
    public function getImageByCode($code);

    /**
     * @param TaxonImageInterface $image
     */
    public function removeImage(TaxonImageInterface $image);

    /**
     * @param TaxonImageInterface $image
     */
    public function addImage(TaxonImageInterface $image);

}
