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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductImage::class);
    }

    function it_implements_product_image_interface()
    {
        $this->shouldImplement(ProductImageInterface::class);
    }

    function it_extends_an_image()
    {
        $this->shouldHaveType(Image::class);
    }

    function it_does_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_have_file_by_default()
    {
        $this->hasFile()->shouldReturn(false);
        $this->getFile()->shouldReturn(null);
    }

    function its_file_is_mutable()
    {
        $file = new \SplFileInfo(__FILE__);
        $this->setFile($file);
        $this->getFile()->shouldReturn($file);
    }

    function its_path_is_mutable()
    {
        $this->setPath(__FILE__);
        $this->getPath()->shouldReturn(__FILE__);
    }

    function it_does_not_have_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable()
    {
        $this->setType('banner');
        $this->getType()->shouldReturn('banner');
    }

    function it_does_not_have_owner_by_default()
    {
        $this->getOwner()->shouldReturn(null);
    }

    function its_owner_is_mutable()
    {
        $owner = new \stdClass();

        $this->setOwner($owner);
        $this->getOwner()->shouldReturn($owner);
    }

    function it_initializes_product_variants_collection_by_default()
    {
        $this->getProductVariants()->shouldHaveType(Collection::class);
    }

    function it_does_not_have_any_product_variants_by_default()
    {
        $this->hasProductVariants()->shouldReturn(false);
    }

    function it_adds_product_variants(ProductVariantInterface $firstVariant, ProductVariantInterface $secondVariant)
    {
        $this->addProductVariant($firstVariant);

        $this->hasProductVariant($firstVariant)->shouldReturn(true);
        $this->hasProductVariants()->shouldReturn(true);

        $this->hasProductVariant($secondVariant)->shouldReturn(false);
    }

    function it_removes_product_variants(ProductVariantInterface $firstVariant, ProductVariantInterface $secondVariant)
    {
        $this->addProductVariant($firstVariant);
        $this->addProductVariant($secondVariant);

        $this->removeProductVariant($firstVariant);

        $this->hasProductVariant($firstVariant)->shouldReturn(false);
        $this->hasProductVariants()->shouldReturn(true);

        $this->hasProductVariant($secondVariant)->shouldReturn(true);
    }
}
