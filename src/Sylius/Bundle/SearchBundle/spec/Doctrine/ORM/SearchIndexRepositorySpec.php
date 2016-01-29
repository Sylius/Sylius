<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class SearchIndexRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManager $em,
        BaseProductRepository $productRepository
    ) {
        $this->beConstructedWith($em, $productRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository');
    }
}
