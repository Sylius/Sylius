<?php

namespace Smile\Bundle\StoreBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('smile:store:list')
            ->setDescription('list known stores');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getContainer()->get('sylius.repository.store');

        /** @var \Symfony\Component\Console\Helper\TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Code', 'URL', 'Parent'));
        /** @var \Smile\Component\Store\Model\StoreInterface $store */
        foreach ($repo->findAll() as $store) {
            $parent = $store->getParent() ? $store->getParent()->getCode() : '';
            $table->addRow(array($store->getCode(), $store->getUrl(), $parent));
        }

        $table->render($output);
    }
}