<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Indexer;

use Doctrine\ORM\EntityManager;
use FOS\ElasticaBundle\Command\PopulateCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * ElasticSearch indexer
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchIndexer implements IndexerInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $output;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(EntityManager $em = null)
    {
        $command = new PopulateCommand();
        $command->setContainer($this->kernel->getContainer());

        $output = new BufferedOutput();
        $input = new ArgvInput(['env' => $this->kernel->getEnvironment()]);
        if ($command->run($input, $output)) { //return code is not zero
            throw new \RuntimeException($output->fetch());
        }

        $this->output = $output->fetch();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }
}
