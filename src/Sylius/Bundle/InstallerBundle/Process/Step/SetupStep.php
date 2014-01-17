<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Process\Step;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

class SetupStep extends ControllerStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        return $this->render(
            'SyliusInstallerBundle:Process/Step:setup.html.twig',
            array('form' => $this->createForm('sylius_setup')->createView())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createForm('sylius_setup');
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $params = $this->get('doctrine')->getConnection()->getParams();
            $dbname = $params['dbname'];
            unset($params['dbname']);

            $schemaManager = DriverManager::getConnection($params)->getSchemaManager();
            if (!in_array($dbname, $schemaManager->listDatabases())) {
                $schemaManager->createDatabase($dbname);
            }
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($em->getMetadataFactory()->getAllMetadata());
            $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

            if ($form->get('load_fixtures')->getData()) {
                $loader = new ContainerAwareLoader($this->container);
                foreach ($this->get('kernel')->getBundles() as $bundle) {
                    if (is_dir($path = $bundle->getPath().'/DataFixtures/ORM')) {
                        $loader->loadFromDirectory($path);
                    }
                }
                $purger = new ORMPurger($em);
                $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
                $executor = new ORMExecutor($em, $purger);
                $executor->execute($loader->getFixtures());
            }

            $user = $form->getData();
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_SYLIUS_ADMIN'));

            $em->persist($user);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->add('success', $this->get('translator')->trans('sylius.flashes.installed'))
            ;

            return $this->complete();
        }

        return $this->render(
            'SyliusInstallerBundle:Process/Step:setup.html.twig',
            array('form' => $form->createView())
        );
    }
}
