<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Factory;
 
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapFactory');
    }

    function it_implements_sitemap_factory_interface()
    {
        $this->shouldImplement(SitemapFactoryInterface::class);
    }

    function it_creates_empty_sitemap(SitemapInterface $sitemap)
    {
        $sitemap->getUrls()->willReturn([]);
        $sitemap->getLastModification()->willReturn(null);
        $sitemap->getLocalization()->willReturn(null);

        $this->createNew()->shouldBeSameAs($sitemap);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof SitemapInterface || !$key instanceof SitemapInterface) {
                    return false;
                }

                return $subject->getLastModification() === $key->getLastModification()
                    && $subject->getLocalization() === $key->getLocalization()
                    && $subject->getUrls() === $key->getUrls()
                ;
            },
        ];
    }
}
