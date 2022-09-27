<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultLocalesStoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultLocalesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_locales(): void
    {
        /** @var DefaultLocalesStoryInterface $defaultLocalesStory */
        $defaultLocalesStory = self::getContainer()->get('sylius.data_fixtures.story.default_locales');

        $defaultLocalesStory->build();

        $this->assertLocaleIsOnDatabase('en_US');
        $this->assertLocaleIsOnDatabase('de_DE');
        $this->assertLocaleIsOnDatabase('fr_FR');
        $this->assertLocaleIsOnDatabase('pl_PL');
        $this->assertLocaleIsOnDatabase('es_ES');
        $this->assertLocaleIsOnDatabase('es_MX');
        $this->assertLocaleIsOnDatabase('pt_PT');
        $this->assertLocaleIsOnDatabase('zh_CN');
    }

    private function assertLocaleIsOnDatabase(string $localeCode)
    {
        /** @var RepositoryInterface $localeRepository */
        $localeRepository = self::getContainer()->get('sylius.repository.locale');
        $locale = $localeRepository->findOneBy(['code' => $localeCode]);

        $this->assertNotNull($locale, sprintf('Locale "%s" should be on database but it does not.', $localeCode));
    }
}
