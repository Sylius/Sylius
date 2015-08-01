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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\Process\Process;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExportProfileController extends ResourceController
{
    /**
     * @param string $code
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function exportAction($code)
    {
        $processPath = sprintf('%s/console sylius:export %s --env %s',
            $this->getParameter('kernel.root_dir'),
            $code,
            $this->getParameter('kernel.environment'));

        $process = new Process($processPath);
        $process->mustRun();

        $exportProfile = $this->container->get('sylius.repository.export_profile')->findOneBy(
            array(
                'code' => $code,
            )
        );
        $exportJob = $exportProfile->getJobs()->last();

        return $this->redirect(
            $this->generateUrl(
                'sylius_backend_export_job_show',
                array(
                    'profileId' => $exportProfile->getId(),
                    'id' => $exportJob->getId(),
                )
            )
        );
    }
}
