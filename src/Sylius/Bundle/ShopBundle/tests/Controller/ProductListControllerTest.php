<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\ShopBundle\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Controller\ProductListController;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

#[CoversClass(ProductListController::class)]
final class ProductListControllerTest extends TestCase
{
    private Environment&MockObject $twig;

    private Stub&TaxonRepositoryInterface $taxonRepository;

    private ProductListController $controller;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->taxonRepository = $this->createStub(TaxonRepositoryInterface::class);

        $localeContext = $this->createStub(LocaleContextInterface::class);
        $localeContext->method('getLocaleCode')->willReturn('en_US');

        $this->controller = new ProductListController($this->twig, $this->taxonRepository, $localeContext);
    }

    #[Test]
    public function it_renders_the_list_of_all_products(): void
    {
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@SyliusShop/product/live_index.html.twig', ['taxon' => null])
            ->willReturn('products')
        ;

        $this->assertSame('products', ($this->controller)()->getContent());
    }

    #[Test]
    public function it_renders_the_list_of_products_of_a_taxon(): void
    {
        $taxon = $this->createStub(TaxonInterface::class);
        $this->taxonRepository->method('findOneBySlug')->willReturn($taxon);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@SyliusShop/product/live_index.html.twig', ['taxon' => $taxon])
            ->willReturn('caps')
        ;

        $this->assertSame('caps', ($this->controller)('caps')->getContent());
    }

    #[Test]
    public function it_throws_a_not_found_exception_when_the_taxon_does_not_exist(): void
    {
        $this->taxonRepository->method('findOneBySlug')->willReturn(null);
        $this->twig->expects($this->never())->method('render');

        $this->expectException(NotFoundHttpException::class);

        ($this->controller)('unknown');
    }
}
