<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Provider;
 
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\ChangeFrequency;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Provider\UrlProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductUrlProviderSpec extends ObjectBehavior
{
    function let(ProductRepository $repository, RouterInterface $router, SitemapUrlFactoryInterface $sitemapUrlFactory)
    {
        $this->beConstructedWith($repository, $router, $sitemapUrlFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Provider\ProductUrlProvider');
    }

    function it_implements_provider_interface()
    {
        $this->shouldImplement(UrlProviderInterface::class);
    }

    function it_generates_urls(
        $repository,
        $router,
        $sitemapUrlFactory,
        Collection $products,
        \Iterator $iterator,
        ProductInterface $product,
        SitemapUrlInterface $sitemapUrl,
        \DateTime $now
    ) {
        $repository->findAll()->willReturn($products);
        $products->getIterator()->willReturn($iterator);
        $iterator->valid()->willReturn(true, false);
        $iterator->next()->shouldBeCalled();
        $iterator->rewind()->shouldBeCalled();

        $iterator->current()->willReturn($product);
        $product->getUpdatedAt()->willReturn($now);

        $router->generate($product, [], true)->willReturn('http://sylius.org/t-shirt');
        $sitemapUrlFactory->createNew()->willReturn($sitemapUrl);

        $sitemapUrl->setLocalization('http://sylius.org/t-shirt')->shouldBeCalled();
        $sitemapUrl->setLastModification($now)->shouldBeCalled();
        $sitemapUrl->setChangeFrequency(ChangeFrequency::always())->shouldBeCalled();
        $sitemapUrl->setPriority(0.5)->shouldBeCalled();

        $this->generate();
    }
}
