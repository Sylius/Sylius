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

namespace Tests\Sylius\Bundle\CoreBundle\Installation\Setup;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class LocaleSetupTest extends KernelTestCase
{
    private Filesystem $filesystem;

    private string $originalLocale;

    private MockObject&RepositoryInterface $localeRepository;

    private FactoryInterface&MockObject $localeFactory;

    private LocaleSetup $localeSetup;

    private string $localeParameterFilePath;

    protected function setUp(): void
    {
        self::bootKernel();

        // Store original locale and set consistent locale for tests
        $this->originalLocale = \Locale::getDefault();
        \Locale::setDefault('en_US');

        $this->filesystem = new Filesystem();
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->localeFactory = $this->createMock(FactoryInterface::class);
        $this->localeParameterFilePath = self::$kernel->getProjectDir() . '/var/temporary_services_file.yaml';
        $this->createTemporaryServicesFile(['parameters' => ['locale' => 'en_US']]);

        $this->localeSetup = new LocaleSetup(
            $this->localeRepository,
            $this->localeFactory,
            'en_US',
            $this->filesystem,
            $this->localeParameterFilePath,
        );
    }

    protected function tearDown(): void
    {
        \Locale::setDefault($this->originalLocale);

        parent::tearDown();
    }

    #[Test]
    public function it_updates_locale_with_a_given_one_if_it_is_different_than_default_one(): void
    {
        $questionHelper = $this->createMock(QuestionHelper::class);
        $questionHelper
            ->expects($this->once())
            ->method('ask')
            ->willReturn('fr_FR');

        $locale = $this->createMock(LocaleInterface::class);

        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'fr_FR'])
            ->willReturn(null);

        $this->localeFactory
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($locale);

        $locale
            ->expects($this->once())
            ->method('setCode')
            ->with('fr_FR');

        $this->localeRepository
            ->expects($this->once())
            ->method('add')
            ->with($locale);

        $this->localeSetup->setup(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
            $questionHelper,
        );

        $fileContent = Yaml::parseFile($this->localeParameterFilePath);
        $this->assertEquals('fr_FR', $fileContent['parameters']['locale']);

        unlink($this->localeParameterFilePath);
    }

    #[Test]
    public function it_does_not_update_locale_with_existing_locale(): void
    {
        $questionHelper = $this->createMock(QuestionHelper::class);
        $questionHelper
            ->expects($this->once())
            ->method('ask')
            ->willReturn('en_US');

        $locale = $this->createMock(LocaleInterface::class);

        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'en_US'])
            ->willReturn($locale);

        $this->localeFactory
            ->expects($this->never())
            ->method('createNew');

        $locale
            ->expects($this->never())
            ->method('setCode');

        $this->localeRepository
            ->expects($this->never())
            ->method('add');

        $this->localeSetup->setup(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
            $questionHelper,
        );

        $this->assertEquals('en_US', Yaml::parseFile($this->localeParameterFilePath)['parameters']['locale']);

        unlink($this->localeParameterFilePath);
    }

    #[Test]
    public function it_shows_message_at_output_when_the_file_does_not_exists_or_path_is_null(): void
    {
        unlink($this->localeParameterFilePath);

        $questionHelper = $this->createMock(QuestionHelper::class);
        $questionHelper
            ->expects($this->once())
            ->method('ask')
            ->willReturn('fr_FR');

        $locale = $this->createMock(LocaleInterface::class);

        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'fr_FR'])
            ->willReturn(null);

        $this->localeFactory
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($locale);

        $locale
            ->expects($this->once())
            ->method('setCode')
            ->with('fr_FR');

        $this->localeRepository
            ->expects($this->once())
            ->method('add')
            ->with($locale);

        $output = $this->createMock(OutputInterface::class);
        $output
            ->expects($this->exactly(3))
            ->method('writeln')
            ->with($this->logicalOr(
                'Adding <info>French</info> Language.',
                'Adding <info>fr_FR</info> locale.',
                '<info>You may also need to add this locale into config/parameters.yaml configuration.</info>',
            ));

        $this->localeSetup->setup(
            $this->createMock(InputInterface::class),
            $output,
            $questionHelper,
        );
    }

    #[Test]
    public function it_does_not_update_locale_if_file_is_not_writable(): void
    {
        $this->localeSetup = new LocaleSetup(
            $this->localeRepository,
            $this->localeFactory,
            'en_US',
            $this->filesystem,
            $this->localeParameterFilePath,
        );

        $this->filesystem->chmod($this->localeParameterFilePath, 0444);

        $questionHelper = $this->createMock(QuestionHelper::class);
        $questionHelper
            ->expects($this->once())
            ->method('ask')
            ->willReturn('fr_FR');

        $locale = $this->createMock(LocaleInterface::class);

        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'fr_FR'])
            ->willReturn(null);

        $this->localeFactory
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($locale);

        $locale
            ->expects($this->once())
            ->method('setCode')
            ->with('fr_FR');

        $this->localeRepository
            ->expects($this->once())
            ->method('add')
            ->with($locale);

        $output = $this->createMock(OutputInterface::class);
        $output
            ->expects($this->exactly(3))
            ->method('writeln')
            ->with($this->logicalOr(
                'Adding <info>French</info> Language.',
                'Adding <info>fr_FR</info> locale.',
                '<info>You may also need to add this locale into config/parameters.yaml configuration.</info>',
            ));

        $this->localeSetup->setup(
            $this->createMock(InputInterface::class),
            $output,
            $questionHelper,
        );

        $this->assertEquals('en_US', Yaml::parseFile($this->localeParameterFilePath)['parameters']['locale']);

        unlink($this->localeParameterFilePath);
    }

    private function createTemporaryServicesFile(array $parameters): void
    {
        $content = Yaml::dump($parameters);
        file_put_contents($this->localeParameterFilePath, $content);
    }
}
