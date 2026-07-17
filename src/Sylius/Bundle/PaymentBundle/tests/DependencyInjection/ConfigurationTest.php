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

namespace Tests\Sylius\Bundle\PaymentBundle\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\DependencyInjection\Configuration;

#[IgnoreDeprecations]
final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function it_turns_on_encryption_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['encryption' => ['enabled' => true]],
            'encryption.enabled',
        );
    }

    #[Test]
    public function its_encryption_can_be_turned_off(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['enabled' => false]]],
            ['encryption' => ['enabled' => false]],
            'encryption.enabled',
        );
    }

    #[Test]
    public function it_treats_null_like_true_in_gateways_encryption_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['disabled_for_factories' => ['offline']]]],
            ['encryption' => ['disabled_for_factories' => ['offline']]],
            'encryption.disabled_for_factories',
        );
    }

    #[Test]
    public function it_can_configure_not_encrypted_gateways(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['disabled_for_factories' => ['offline']]]],
            ['encryption' => ['disabled_for_factories' => ['offline']]],
            'encryption.disabled_for_factories',
        );
    }

    #[Test]
    public function it_disables_strict_decryption_mode_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['encryption' => ['strict_mode' => false]],
            'encryption.strict_mode',
        );
    }

    #[Test]
    public function its_strict_decryption_mode_can_be_enabled(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['strict_mode' => true]]],
            ['encryption' => ['strict_mode' => true]],
            'encryption.strict_mode',
        );
    }

    #[Test]
    public function it_allows_all_classes_during_decryption_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['encryption' => ['allowed_classes' => true]],
            'encryption.allowed_classes',
        );
    }

    #[Test]
    public function it_can_configure_an_explicit_list_of_allowed_classes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['allowed_classes' => [\stdClass::class]]]],
            ['encryption' => ['allowed_classes' => [\stdClass::class]]],
            'encryption.allowed_classes',
        );
    }

    #[Test]
    public function it_triggers_a_deprecation_when_all_classes_are_allowed_during_decryption(): void
    {
        $this->expectUserDeprecationMessage('Since sylius/payment-bundle 2.3: Allowing all classes during payment data decryption by setting "sylius_payment.encryption.allowed_classes" to "true" is deprecated and will not be the default in Sylius 3.0. Set it to "false" or provide an explicit list of allowed class-strings.');

        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['allowed_classes' => true]]],
            ['encryption' => ['allowed_classes' => true]],
            'encryption.allowed_classes',
        );
    }

    #[Test]
    public function it_does_not_allow_a_scalar_value_for_allowed_classes(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [['encryption' => ['allowed_classes' => 'stdClass']]],
            'encryption.allowed_classes',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
