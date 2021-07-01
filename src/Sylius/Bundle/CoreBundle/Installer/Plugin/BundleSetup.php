<?php

namespace Sylius\Bundle\CoreBundle\Installer\Plugin;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BundleSetup
{
    private string $namespace;
    private string $className;
    public function setup(ComposerSetup $composerSetup, OutputInterface $output): void
    {
        $this->namespace = $composerSetup->getNamespace();
        $this->className = str_replace('\\', '', $this->namespace);
        $output->writeln(sprintf('Preparing to install the <info>bundle</info> class: <comment>%s\\%s</comment>', $this->namespace, $this->className));
    }

    public function install(OutputInterface $output): void
    {
        $filecontent = $this->renderConfigurationFile($this->namespace, $this->className);
        file_put_contents(sprintf(__DIR__ . '/../../../../../../../../../src/%s.php', $this->className), $filecontent);
        $output->writeln(sprintf('The bundle was successful installed on <info>%s</info>', $this->className));
    }

    private function renderConfigurationFile(string $namespace, string $className): string
    {
        return <<<FILE
<?php

declare(strict_types=1);

namespace {$namespace};

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class MartXTeamSyliusProductBiddingPlugin extends Bundle
{
    const VERSION = '0.0.0';
    use SyliusPluginTrait;
}

FILE;
    }
}
