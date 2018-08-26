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
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Brett Bailey <bretto36@gmail.com>
 */
class DeleteAdminUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:admin-user:delete')
            ->setDescription('Deletes an administrator account.')
            ->setDefinition([
                new InputArgument('identifier', InputArgument::REQUIRED, 'Identified (username or email)'),
            ])
            ->setHelp(<<<EOT
The <info>%command.name%</info> command deletes an admin account.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = $input->getArgument('identifier');

        /** @var AdminUserInterface $user */
        $user = $this->getUserProvider()->loadUserByUsername($identifier);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('Could not find user identified by identifier "%s"', $identifier));
        }

        $manager = $this->getEntityManager();
        $manager->remove($user);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('identifier')) {
            $identifier = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an identifier (email or username):',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Identifier can not be empty');
                    }

                    return $username;
                }
            );

            $input->setArgument('identifier', $identifier);
        }
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('sylius.manager.admin_user');
    }

    /**
     * @return EntityRepository
     */
    protected function getUserProvider()
    {
        return $this->getContainer()->get('sylius.admin_user.provider.email_or_name_based');
    }
}
