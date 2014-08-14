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

use Sylius\Bundle\CoreBundle\Kernel\Kernel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManager;

/**
 * Elasticsearch indexer
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticSearchIndexer implements IndexerInterface
{
    /* @var \Sylius\Bundle\CoreBundle\Kernel\Kernel */
    private $kernel;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(EntityManager $em = null, OutputInterface $output = null)
    {
        $environment = $this->kernel->getEnvironment();

        $populateCommand = sprintf("/console fos:elastica:populate --env=%s", $environment);

        $command = $this->kernel->getRootDir() . $populateCommand;
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        print $process->getOutput();
    }

} 