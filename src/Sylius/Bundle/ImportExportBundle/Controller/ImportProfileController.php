<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\Process\Process;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportProfileController extends ResourceController
{
    /**
     * @param string $code
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importAction($code)
    {
        $processPath = sprintf('%s/console sylius:import %s --env %s',
            $this->getParameter('kernel.root_dir'),
            $code,
            $this->getParameter('kernel.environment'));

        $process = new Process($processPath);
        $process->mustRun();

        $importProfile = $this->container->get('sylius.repository.import_profile')->findOneBy(array(
            'code' => $code,
        ));
        if (null === $importProfile) {
            throw new EntityNotFoundException();
        }

        $importJobs = $importProfile->getJobs();
        if(empty($importJobs)) {
            throw new EntityNotFoundException();
        }

        return $this->redirect(
            $this->generateUrl(
                'sylius_backend_import_job_show',
                array(
                    'profileId' => $importProfile->getId(),
                    'id' => $importJobs->last()->getId(),
                )
            )
        );
    }
}
