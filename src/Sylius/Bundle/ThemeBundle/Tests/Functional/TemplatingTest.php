<?php

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplatingTest extends WebTestCase
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

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, $crawler->text());
    }

    /**
     * @return array
     */
    public function getBundleTemplates()
    {
        return [
            ['TestBundle:Templating:vanillaTemplate.txt.twig', 'TestBundle:Templating:vanillaTemplate.txt.twig'],
            ['TestBundle:Templating:vanillaOverridedTemplate.txt.twig', 'TestBundle:Templating:vanillaOverridedTemplate.txt.twig (app overrided)'],
            ['TestBundle:Templating:vanillaOverridedThemeTemplate.txt.twig', 'TestBundle:Templating:vanillaOverridedThemeTemplate.txt.twig|sylius/first-test-theme'],
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

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, $crawler->text());
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

    protected function setUp()
    {
        parent::setUp();

        $this->deleteTmpDir('ThemeBundleTest');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteTmpDir('ThemeBundleTest');
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        $client = $this->createClient(array('test_case' => 'DefaultTestCase', 'root_config' => 'config.yml'));
        try {
            $client->insulate();
        } catch (\RuntimeException $e) {
            // Don't insulate requests if not possible to do so.
        }

        return $client;
    }
}
