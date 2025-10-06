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

use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;
use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Knp\Bundle\GaufretteBundle\KnpGaufretteBundle;
use League\FlysystemBundle\FlysystemBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Payum\Bundle\PayumBundle\PayumBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Sylius\Abstraction\StateMachine\SyliusStateMachineAbstractionBundle;
use Sylius\Bundle\AddressingBundle\SyliusAddressingBundle;
use Sylius\Bundle\ApiBundle\SyliusApiBundle;
use Sylius\Bundle\AttributeBundle\SyliusAttributeBundle;
use Sylius\Bundle\ChannelBundle\SyliusChannelBundle;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle;
use Sylius\Bundle\CustomerBundle\SyliusCustomerBundle;
use Sylius\Bundle\FixturesBundle\SyliusFixturesBundle;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Bundle\InventoryBundle\SyliusInventoryBundle;
use Sylius\Bundle\LocaleBundle\SyliusLocaleBundle;
use Sylius\Bundle\MailerBundle\SyliusMailerBundle;
use Sylius\Bundle\MoneyBundle\SyliusMoneyBundle;
use Sylius\Bundle\OrderBundle\SyliusOrderBundle;
use Sylius\Bundle\PaymentBundle\SyliusPaymentBundle;
use Sylius\Bundle\PayumBundle\SyliusPayumBundle;
use Sylius\Bundle\ProductBundle\SyliusProductBundle;
use Sylius\Bundle\PromotionBundle\SyliusPromotionBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ReviewBundle\SyliusReviewBundle;
use Sylius\Bundle\ShippingBundle\SyliusShippingBundle;
use Sylius\Bundle\TaxationBundle\SyliusTaxationBundle;
use Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle;
use Sylius\Bundle\UserBundle\SyliusUserBundle;
use SyliusLabs\DoctrineMigrationsExtraBundle\SyliusLabsDoctrineMigrationsExtraBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

return [
    FrameworkBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    SyliusStateMachineAbstractionBundle::class => ['all' => true],
    SyliusOrderBundle::class => ['all' => true],
    SyliusMoneyBundle::class => ['all' => true],
    SyliusCurrencyBundle::class => ['all' => true],
    SyliusLocaleBundle::class => ['all' => true],
    SyliusProductBundle::class => ['all' => true],
    SyliusChannelBundle::class => ['all' => true],
    SyliusAttributeBundle::class => ['all' => true],
    SyliusTaxationBundle::class => ['all' => true],
    SyliusShippingBundle::class => ['all' => true],
    SyliusPaymentBundle::class => ['all' => true],
    SyliusMailerBundle::class => ['all' => true],
    SyliusPromotionBundle::class => ['all' => true],
    SyliusAddressingBundle::class => ['all' => true],
    SyliusInventoryBundle::class => ['all' => true],
    SyliusTaxonomyBundle::class => ['all' => true],
    SyliusUserBundle::class => ['all' => true],
    SyliusCustomerBundle::class => ['all' => true],
    SyliusReviewBundle::class => ['all' => true],
    SyliusCoreBundle::class => ['all' => true],
    SyliusResourceBundle::class => ['all' => true],
    SyliusGridBundle::class => ['all' => true],
    KnpGaufretteBundle::class => ['all' => true],
    FlysystemBundle::class => ['all' => true],
    LiipImagineBundle::class => ['all' => true],
    StofDoctrineExtensionsBundle::class => ['all' => true],
    BabDevPagerfantaBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    PayumBundle::class => ['all' => true],
    SyliusPayumBundle::class => ['all' => true],
    SyliusFixturesBundle::class => ['all' => true],
    ApiPlatformBundle::class => ['all' => true],
    SyliusApiBundle::class => ['all' => true],
    DebugBundle::class => ['dev' => true, 'test' => true, 'test_cached' => true],
    NelmioAliceBundle::class => ['dev' => true, 'test' => true, 'test_cached' => true],
    FidryAliceDataFixturesBundle::class => ['dev' => true, 'test' => true, 'test_cached' => true],
    LexikJWTAuthenticationBundle::class => ['all' => true],
    SyliusLabsDoctrineMigrationsExtraBundle::class => ['all' => true],
    WebpackEncoreBundle::class => ['all' => true],
];
