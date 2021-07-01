<?php

namespace Sylius\Bundle\CoreBundle\Installer\Plugin;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ComposerSetup
{
    protected string $vendorName = 'Acme';
    protected string $pluginName = 'Greeting';
    protected string $description = 'Acme example plugin for Sylius.';
    protected string $license = 'MIT';
    private string $namespace;

    private static $dataToBeChange = [
        'name',
        'description',
        'license',
        'autoload',
        'autoload-dev'
    ];

    private AsciiSlugger $slugger;
    private Serializer $serializer;
    private array $previousComposer = [];
    private array $currentComposer = [];
    private string $composerFilename;


    public function __construct(AsciiSlugger $slugger, Serializer $serializer)
    {
        $this->slugger = $slugger;
        $this->serializer = $serializer;
        $this->composerFilename = realpath(__DIR__ . '/../../../../../../../../../composer.json');
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): void
    {
        $vendorName = $this->getInputVendorName($input, $output, $questionHelper);
        $pluginName = $this->getInputPluginName($input, $output, $questionHelper);
        $description = $this->getInputDescription($input, $output, $questionHelper);
        $license = $this->getInputLicense($input, $output, $questionHelper);


        $this->currentComposer = $this->readComposerFileContent();

        $this->currentComposer['name'] = $this->convertToPackageName($vendorName, $pluginName);
        $this->currentComposer['description'] = $description;
        $this->currentComposer['license'] = $license;

        $this->namespace = sprintf('%s\\%s', $vendorName, $pluginName);
        $this->currentComposer['autoload']['psr-4'][$this->namespace . '\\'] = 'src/';

        $namespaceTest = sprintf('Tests\\%s', $this->namespace);
        $this->currentComposer['autoload-dev']['psr-4'][$namespaceTest . '\\'] = 'tests/';

        $table = new Table($output);
        $table->setHeaders(['attribute', 'current']);
        $table->addRows(
            array_map(function ($key) {
                return [
                    $key,
                    $this->encodeJSON($this->currentComposer[$key], JSON_PRETTY_PRINT),
                ];
            }, self::$dataToBeChange)
        );
        $table->render();
    }

    public function install(OutputInterface $output): void
    {
        $this->writeComposerFileContent($this->currentComposer);
        $output->writeln('The changes on composer was successfully applied.');
        $output->writeln('Now you must execute the command: <comment>composer dump-autoload</comment>');
    }

    private function getInputData($type, $defaultValue, InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $question = new Question(sprintf('Define the %s (press enter to use "%s"): ', $type, $defaultValue), $defaultValue);

        return trim($questionHelper->ask($input, $output, $question));
    }

    private function getInputVendorName(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        return $this->getInputData('vendor name', $this->vendorName, $input, $output, $questionHelper);
    }

    private function getInputPluginName(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $pluginName = $this->getInputData('plugin name', $this->pluginName, $input, $output, $questionHelper);
        return sprintf('Sylius%sPlugin', $pluginName);
    }

    private function getInputDescription(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        return $this->getInputData('description', $this->description, $input, $output, $questionHelper);
    }

    private function getInputLicense(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        return $this->getInputData('license', $this->license, $input, $output, $questionHelper);
    }

    private function readComposerFileContent(): array
    {
        return $this->decodeJSON(file_get_contents($this->composerFilename));
    }

    private function writeComposerFileContent(array $composerContent)
    {
        return file_put_contents($this->composerFilename, $this->encodeJSON($composerContent, JSON_PRETTY_PRINT));
    }

    private function decodeJSON(string $data)
    {
        return $this->serializer->decode($data, 'json');
    }

    private function encodeJSON($data, $options = null): string
    {
        $attributes = [
            JsonEncode::OPTIONS => JSON_UNESCAPED_SLASHES,
        ];
        if ($options) {
            $attributes[JsonEncode::OPTIONS] |= $options;
        }
        return $this->serializer->encode($data, 'json', $attributes);
    }

    private function convertToPackageName(string $vendorName, string $pluginName): string
    {
        return sprintf('%s/%s', $this->convertToDashCase($vendorName), $this->convertToDashCase($pluginName));
    }

    private function convertToDashCase($text): string
    {
        return str_replace('_', '-', $this->slugger->slug($text)->snake());
    }
}
