<?php

namespace Sylius\Bundle\CoreBundle\Installer\Plugin;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class ConfigurationSetup
{
    private string $namespace;
    private string $containerAlias;
    private string $filePath;

    public function setup(ComposerSetup $consumerSetup, OutputInterface $output): void
    {
        $this->namespace = $consumerSetup->getNamespace();
        $this->containerAlias = Container::underscore(str_replace('\\', '', $this->namespace));
        $this->filePath = __DIR__ . '/../../../../../../../../../src/DependencyInjection/Configuration.php';
        $output->writeln(sprintf('Preparing to install the <info>service</info> configuration class: <comment>%s</comment>', $this->containerAlias));
    }

    public function install(OutputInterface $output): void
    {
        $filecontent = $this->renderConfigurationFile($this->namespace, $this->containerAlias);
        file_put_contents($this->filePath, $filecontent);
        $output->writeln(sprintf('The service was successful installed', $this->containerAlias));
    }

    private function renderConfigurationFile(string $namespace, string $containerAlias): string
    {
        return <<<FILE
<?php

declare(strict_types=1);

namespace {$namespace}\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        \$treeBuilder = new TreeBuilder('{$containerAlias}');
        \$rootNode = \$treeBuilder->getRootNode();

        return \$treeBuilder;
    }
}

FILE;
    }
}
