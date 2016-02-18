<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class DeleteUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:user:delete')
            ->setDescription('Deletes a user/customer account.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
            ])
            ->setHelp(<<<EOT
The <info>%command.name%</info> command deletes a user account and it's customer information.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $manager = $this->getEntityManager();

        /** @var UserInterface $user */
        $user = $this->getUserRepository()->findOneByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('Could not find user identified by email "%s"', $email));
        }

        $manager->remove($user);
        $manager->flush();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('sylius.manager.user');
    }

    /**
     * @return EntityRepository
     */
    protected function getUserRepository()
    {
        return $this->getContainer()->get('sylius.repository.user');
    }
}
