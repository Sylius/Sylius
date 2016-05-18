<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplatingTest extends ThemeBundleTestCase
{
    /**
     * @dataProvider getBundleTemplates
     *
     * @param string $templateName
     * @param string $contents
     */
    public function testRenderBundleTemplates($templateName, $contents)
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/template/'.$templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getBundleTemplates()
    {
        return [
            ['TestBundle:Templating:vanillaTemplate.txt.twig', 'TestBundle:Templating:vanillaTemplate.txt.twig'],
            ['TestBundle:Templating:vanillaOverriddenTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenTemplate.txt.twig (app overridden)'],
            ['TestBundle:Templating:vanillaOverriddenThemeTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenThemeTemplate.txt.twig|sylius/first-test-theme'],
            ['TestBundle:Templating:bothThemesTemplate.txt.twig', 'TestBundle:Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'],
            ['TestBundle:Templating:lastThemeTemplate.txt.twig', 'TestBundle:Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'],
        ];
    }

    /**
     * @dataProvider getAppTemplates
     *
     * @param string $templateName
     * @param string $contents
     */
    public function testRenderAppTemplates($templateName, $contents)
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/template/'.$templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getAppTemplates()
    {
        return [
            [':Templating:vanillaTemplate.txt.twig', ':Templating:vanillaTemplate.txt.twig'],
            [':Templating:bothThemesTemplate.txt.twig', ':Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'],
            [':Templating:lastThemeTemplate.txt.twig', ':Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'],
        ];
    }
}
