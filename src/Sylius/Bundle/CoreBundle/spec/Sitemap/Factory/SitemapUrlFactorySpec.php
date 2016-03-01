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
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapUrlFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactory');
    }

    function it_implements_sitemap_url_factory_interface()
    {
        $this->shouldImplement(SitemapUrlFactoryInterface::class);
    }

    function it_creates_empty_sitemap_url(SitemapUrlInterface $sitemapUrl)
    {
        $sitemapUrl->getLastModification()->willReturn(null);
        $sitemapUrl->getLocalization()->willReturn(null);
        $sitemapUrl->getPriority()->willReturn(null);
        $sitemapUrl->getChangeFrequency()->willReturn('');

        $this->createNew()->shouldBeSameAs($sitemapUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof SitemapUrlInterface || !$key instanceof SitemapUrlInterface) {
                    return false;
                }

                return $subject->getChangeFrequency() === $key->getChangeFrequency()
                    && $subject->getLocalization() === $key->getLocalization()
                    && $subject->getLastModification() === $key->getLastModification()
                    && $subject->getPriority() === $key->getPriority();
            },
        ];
    }
}
