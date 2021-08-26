<?php

namespace Sylius\Bundle\ApiBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as CoreProduct;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_product")
 */
class Product extends CoreProduct implements ProductInterface
{
    /**
     * @var int
     */
    private $onHand;

    /**
     * @return int
     */
    public function getOnHand(): ?int
    {
        return $this->onHand;
    }

    /**
     * @param int $onHand
     */
    public function setOnHand(int $onHand): void
    {
        $this->onHand = $onHand;
    }

    /**
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @var int
     */
    private $price;
}
