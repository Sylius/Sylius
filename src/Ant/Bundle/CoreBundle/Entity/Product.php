<?php

namespace Ant\Bundle\CoreBundle\Entity;

use Sylius\Bundle\AssortmentBundle\Model\Product as BaseProduct;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product entity.
 * Produkt bez wariantów, parametrów.
 */
class Product extends BaseProduct
{

    private $enabled;

    public function __construct() {
        parent::__construct();

        $this->enabled = false;
    }
}