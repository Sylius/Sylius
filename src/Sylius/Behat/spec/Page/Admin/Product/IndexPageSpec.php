<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ProductShowPageInterface;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\TableManipulatorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class IndexPageSpec extends ObjectBehavior
{
    function let(Session $session, RouterInterface $router, TableManipulatorInterface $tableManipulator)
    {
        $this->beConstructedWith($session, [], $router, $tableManipulator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Page\Admin\Product\IndexPage');
    }

    function it_is_symfony_page()
    {
        $this->shouldHaveType(SymfonyPage::class);
    }

    function it_implements_product_index_page_interface()
    {
        $this->shouldImplement(IndexPageInterface::class);
    }
}
