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
class TranslationTest extends ThemeBundleTestCase
{
    public function testTranslations()
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/template/:Translation:translationsTest.txt.twig');

        foreach ($this->getTranslationsLines() as $expectedContent) {
            $this->assertContains($expectedContent, $crawler->text());
        }
    }

    /**
     * @return array
     */
    protected function getTranslationsLines()
    {
        return [
            'BUNDLE/Resources/translations: BUNDLE/Resources/translations',
            'app/Resources/BUNDLE_NAME/translations: app/Resources/BUNDLE_NAME/translations',
            'app/Resources/translations: app/Resources/translations',
            'THEME/BUNDLE_NAME/translations: THEME/BUNDLE_NAME/translations',
            'THEME/translations: THEME/translations',
            'PARENT_THEME/BUNDLE_NAME/translations: PARENT_THEME/BUNDLE_NAME/translations',
            'PARENT_THEME/translations: PARENT_THEME/translations',
        ];
    }
}
