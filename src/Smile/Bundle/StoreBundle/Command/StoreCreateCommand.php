<?php

namespace Smile\Bundle\StoreBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('smile:store:create')
            ->setDescription('create a new store')
            ->addArgument('code', InputArgument::REQUIRED, 'Store code')
            ->addArgument('url', InputArgument::REQUIRED, 'Store url')
            ->addArgument('parent', InputArgument::OPTIONAL, 'parent store code');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getContainer()->get('sylius.repository.store');
        $em = $this->getContainer()->get('sylius.manager.store');

        /** @var \Sylius\Component\Store\Model\StoreInterface $store */
        $store = $repo->createNew();
        $store->setCode($input->getArgument('code'));
        $store->setUrl($input->getArgument('url'));

        $parent = $input->getArgument('parent');
        if ($parent) {
            $parentStore = $repo->findOneByCode($parent);
            if ($parentStore) {
                $store->setParent($parentStore);
            } else {
                throw new \Exception(sprintf('No store found with code: %s', $parent));
            }
        }

        $em->persist($store);
        $em->flush();
    }
}