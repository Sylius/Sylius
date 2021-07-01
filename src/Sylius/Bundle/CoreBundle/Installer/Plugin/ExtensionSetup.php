<?php

namespace Sylius\Bundle\CoreBundle\Installer\Plugin;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionSetup
{
    private string $namespace;
    private string $className;
    private string $filePath;

    public function setup(ComposerSetup $composerSetup, OutputInterface $output): void
    {
        $this->namespace = sprintf('%s\\DependencyInjection', $composerSetup->getNamespace());
        $this->className = str_replace(['\\', 'Plugin'], ['', 'Extension'], $composerSetup->getNamespace());
        $this->filePath = sprintf(__DIR__ . '/../../../../../../../../../src/DependencyInjection/%s.php', $this->className);
        $output->writeln(sprintf('Preparing to install the <info>extension</info> class: <comment>%s</comment>', $this->className));
    }

    public function install(OutputInterface $output): void
    {
        $filecontent = $this->renderConfigurationFile($this->namespace, $this->className);
        file_put_contents($this->filePath, $filecontent);
        $output->writeln(sprintf('The extension was successful installed.', $this->className));
    }

    private function renderConfigurationFile(string $namespace, string $className): string
    {
        return <<<FILE
<?php

declare(strict_types=1);

namespace {$namespace};

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class $className extends Extension
{
    public function load(array \$configs, ContainerBuilder \$container): void
    {
        \$config = \$this->processConfiguration(\$this->getConfiguration([], \$container), \$configs);
        \$loader = new XmlFileLoader(\$container, new FileLocator(__DIR__ . '/../Resources/config'));

        \$loader->load('services.xml');
    }

    public function getConfiguration(array \$config, ContainerBuilder \$container): ConfigurationInterface
    {
        return new Configuration();
    }
}

FILE;
    }
}
