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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslationTest extends WebTestCase
{
    /**
     * @test
     */
    public function it_respects_theming_logic_while_translating_messages()
    {
        $client = self::createClient();

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
