<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Command;

use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DebugResourceCommand extends Command
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('sylius:debug:resource');
        $this->setDescription('Debug resource metadata.');
        $this->setHelp(<<<'EOT'
List or show resource metadata.

To list run the command without an agrument:

    $ php %command.full_name%

To show the metadata for a resource, pass its alias:

    $ php %command.full_name% sylius.user
EOT
        );
        $this->addArgument('resource', InputArgument::OPTIONAL, 'Resource to debug');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $input->getArgument('resource');

        if (null === $resource) {
            $this->listResources($output);

            return;
        }

        $metadata = $this->registry->get($resource);

        $this->debugResource($metadata, $output);
    }

    /**
     * @param OutputInterface $output
     */
    private function listResources(OutputInterface $output): void
    {
        $resources = $this->registry->getAll();
        ksort($resources);

        $table = new Table($output);
        $table->setHeaders(['Alias']);

        foreach ($resources as $resource) {
            $table->addRow([$resource->getAlias()]);
        }

        $table->render();
    }

    /**
     * @param MetadataInterface $metadata
     * @param OutputInterface $output
     */
    private function debugResource(MetadataInterface $metadata, OutputInterface $output): void
    {
        $table = new Table($output);
        $information = [
            'name' => $metadata->getName(),
            'application' => $metadata->getApplicationName(),
            'driver' => $metadata->getDriver(),
        ];

        $parameters = $this->flattenParameters($metadata->getParameters());

        foreach ($parameters as $key => $value) {
            $information[$key] = $value;
        }

        foreach ($information as $key => $value) {
            $table->addRow([$key, $value]);
        }

        $table->render();
    }

    /**
     * @param array $parameters
     * @param array $flattened
     * @param string $prefix
     *
     * @return array
     */
    private function flattenParameters(array $parameters, array $flattened = [], $prefix = ''): array
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $flattened = $this->flattenParameters($value, $flattened, $prefix . $key . '.');

                continue;
            }

            $flattened[$prefix . $key] = $value;
        }

        return $flattened;
    }
}
