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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\TaxonFixture;

final class TaxonFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function taxons_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    #[Test]
    public function taxons_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    #[Test]
    public function taxon_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    #[Test]
    public function taxon_slug_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['slug' => 'custom']]]], 'custom.*.slug');
    }

    #[Test]
    public function taxon_children_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['children' => [['name' => 'foo']]]]]], 'custom.*.children');
    }

    #[Test]
    public function taxon_children_may_contain_nested_array(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['children' => [['nested' => ['key' => 'value']]]]]]],
            ['custom' => [['children' => [['nested' => ['key' => 'value']]]]]],
            'custom.*.children',
        );
    }

    #[Test]
    public function taxon_translations_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['translations' => [['en_US' => ['name' => ['foo']]]]]]]], 'custom.*.translations');
    }

    #[Test]
    public function taxon_translations_may_contain_nested_array(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['translations' => [['nested' => ['key' => 'value']]]]]]],
            ['custom' => [['translations' => [['nested' => ['key' => 'value']]]]]],
            'custom.*.translations',
        );
    }

    protected function getConfiguration(): TaxonFixture
    {
        return new TaxonFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
