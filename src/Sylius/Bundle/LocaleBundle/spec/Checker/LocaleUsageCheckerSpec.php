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

namespace spec\Sylius\Bundle\LocaleBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleUsageCheckerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $localeRepository,
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
    ): void {
        $this->beConstructedWith($localeRepository, $registry, $entityManager);
    }

    function it_throws_exception_when_locale_with_provided_locale_code_doesnt_exist(
        RepositoryInterface $localeRepository,
    ): void {
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('isUsed', ['en_US']);
    }

    function it_returns_true_when_at_least_one_usage_of_locale_found(
        EntityRepository $localeRepository,
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
        LocaleInterface $locale,
        MetadataInterface $firstResourceMetadata,
        MetadataInterface $secondResourceMetadata,
    ): void {
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);
        $localeRepository->count(['locale' => 'en_US'])->willReturn(1);

        $registry->getAll()->willReturn([$firstResourceMetadata, $secondResourceMetadata]);

        $firstResourceMetadata->getParameters()->willReturn([
            'translation' => [
                'classes' => [
                    'interface' => 'Sylius\Component\Locale\Model\LocaleInterface',
                ],
            ],
        ]);
        $secondResourceMetadata->getParameters()->willReturn([]);

        $entityManager->getRepository('Sylius\Component\Locale\Model\LocaleInterface')->willReturn($localeRepository);

        $this->isUsed('en_US')->shouldReturn(true);
    }

    function it_returns_false_when_no_usage_of_locale_found(
        EntityRepository $localeRepository,
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
        LocaleInterface $locale,
        MetadataInterface $firstResourceMetadata,
        MetadataInterface $secondResourceMetadata,
    ): void {
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);
        $localeRepository->count(['locale' => 'en_US'])->willReturn(0);

        $registry->getAll()->willReturn([$firstResourceMetadata, $secondResourceMetadata]);

        $firstResourceMetadata->getParameters()->willReturn([
            'translation' => [
                'classes' => [
                    'interface' => 'Sylius\Component\Locale\Model\LocaleInterface',
                ],
            ],
        ]);
        $secondResourceMetadata->getParameters()->willReturn([]);

        $entityManager->getRepository('Sylius\Component\Locale\Model\LocaleInterface')->willReturn($localeRepository);

        $this->isUsed('en_US')->shouldReturn(false);
    }
}
