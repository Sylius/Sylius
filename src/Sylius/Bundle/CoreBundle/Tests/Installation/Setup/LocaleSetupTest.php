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

namespace Tests\Sylius\Bundle\CoreBundle\Installer\Setup;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class LocaleSetupTest extends KernelTestCase
{
    use ProphecyTrait;

    private $filesystem;

    private $localeRepository;

    private $localeFactory;

    private $localeSetup;

    private $localeParameterFilePath;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->filesystem = new Filesystem();
        $this->localeRepository = $this->prophesize(RepositoryInterface::class);
        $this->localeFactory = $this->prophesize(FactoryInterface::class);
        $this->localeParameterFilePath = self::$kernel->getProjectDir() . '/var/temporary_services_file.yaml';
        $this->createTemporaryServicesFile(['parameters' => ['locale' => 'en_US']]);

        $this->localeSetup = new LocaleSetup(
            $this->localeRepository->reveal(),
            $this->localeFactory->reveal(),
            'en_US',
            $this->filesystem,
            $this->localeParameterFilePath,
        );
    }

    /** @test */
    public function it_updates_locale_with_a_given_one_if_it_is_different_than_default_one(): void
    {
        $questionHelper = $this->prophesize(QuestionHelper::class);
        $questionHelper->ask(Argument::cetera())->willReturn('fr_FR');
        $locale = $this->prophesize(LocaleInterface::class);

        $this->localeRepository->findOneBy(['code' => 'fr_FR'])->willReturn(null);
        $this->localeFactory->createNew()->willReturn($locale->reveal());
        $locale->setCode('fr_FR')->shouldBeCalled();
        $this->localeRepository->add($locale->reveal())->shouldBeCalled();

        $this->localeSetup->setup(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal(),
            $questionHelper->reveal(),
        );

        $fileContent = Yaml::parseFile($this->localeParameterFilePath);
        $this->assertEquals('fr_FR', $fileContent['parameters']['locale']);

        unlink($this->localeParameterFilePath);
    }

    /** @test */
    public function it_does_not_update_locale_with_existing_locale(): void
    {
        $questionHelper = $this->prophesize(QuestionHelper::class);
        $questionHelper->ask(Argument::cetera())->willReturn('en_US');
        $locale = $this->prophesize(LocaleInterface::class);

        $this->localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale->reveal());
        $this->localeFactory->createNew()->shouldNotBeCalled();
        $locale->setCode('en_US')->shouldNotBeCalled();
        $this->localeRepository->add($locale->reveal())->shouldNotBeCalled();

        $this->localeSetup->setup(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal(),
            $questionHelper->reveal(),
        );

        $this->assertEquals('en_US', Yaml::parseFile($this->localeParameterFilePath)['parameters']['locale']);

        unlink($this->localeParameterFilePath);
    }

    /** @test */
    public function it_shows_message_at_output_when_the_file_does_not_exists_or_path_is_null(): void
    {
        unlink($this->localeParameterFilePath);

        $questionHelper = $this->prophesize(QuestionHelper::class);
        $questionHelper->ask(Argument::cetera())->willReturn('fr_FR');
        $locale = $this->prophesize(LocaleInterface::class);

        $this->localeRepository->findOneBy(['code' => 'fr_FR'])->willReturn(null);
        $this->localeFactory->createNew()->willReturn($locale->reveal());
        $locale->setCode('fr_FR')->shouldBeCalled();
        $this->localeRepository->add($locale->reveal())->shouldBeCalled();

        $output = $this->prophesize(OutputInterface::class);
        $output->writeln('Adding <info>French</info> Language.')->shouldBeCalled();
        $output->writeln('Adding <info>fr_FR</info> locale.')->shouldBeCalled();
        $output->writeln('<info>You may also need to add this locale into config/parameters.yaml configuration.</info>')->shouldBeCalled();

        $this->localeSetup->setup(
            $this->prophesize(InputInterface::class)->reveal(),
            $output->reveal(),
            $questionHelper->reveal(),
        );
    }

    /** @test */
    public function it_shows_message_at_output_when_the_filesystem_argument_is_null(): void
    {
        $this->localeSetup = new LocaleSetup(
            $this->localeRepository->reveal(),
            $this->localeFactory->reveal(),
            'en_US',
            null,
            $this->localeParameterFilePath,
        );

        $questionHelper = $this->prophesize(QuestionHelper::class);
        $questionHelper->ask(Argument::cetera())->willReturn('fr_FR');
        $locale = $this->prophesize(LocaleInterface::class);

        $this->localeRepository->findOneBy(['code' => 'fr_FR'])->willReturn(null);
        $this->localeFactory->createNew()->willReturn($locale->reveal());
        $locale->setCode('fr_FR')->shouldBeCalled();
        $this->localeRepository->add($locale->reveal())->shouldBeCalled();

        $output = $this->prophesize(OutputInterface::class);
        $output->writeln('Adding <info>French</info> Language.')->shouldBeCalled();
        $output->writeln('Adding <info>fr_FR</info> locale.')->shouldBeCalled();
        $output->writeln('<info>You may also need to add this locale into config/parameters.yaml configuration.</info>')->shouldBeCalled();

        $this->localeSetup->setup(
            $this->prophesize(InputInterface::class)->reveal(),
            $output->reveal(),
            $questionHelper->reveal(),
        );
    }

    private function createTemporaryServicesFile(array $parameters): void
    {
        $content = Yaml::dump($parameters);
        file_put_contents($this->localeParameterFilePath, $content);
    }
}
