<?php

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TranslationTest extends ThemeBundleTestCase
{
    /**
     * @dataProvider getTranslationsLines
     *
     * @param string $contents
     */
    public function testTranslations($contents)
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/template/:Translation:translationsTest.txt.twig');
        $this->assertContains($contents, $crawler->text());
    }

    /**
     * @return array
     */
    public function getTranslationsLines()
    {
        return [
            ['BUNDLE/Resources/translations: BUNDLE/Resources/translations'],
            ['app/Resources/BUNDLE_NAME/translations: app/Resources/BUNDLE_NAME/translations'],
            ['app/Resources/translations: app/Resources/translations'],
            ['THEME/BUNDLE_NAME/translations: THEME/BUNDLE_NAME/translations'],
            ['THEME/translations: THEME/translations'],
        ];
    }
}
