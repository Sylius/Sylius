<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class JobLogController
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobLogController extends ResourceController
{

    /**
     * Log index controller
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $jobId         = $request->get('jobId');
        $jobRepository = $this->get('sylius.repository.job');
        $job           = $jobRepository->find($jobId);

        $contentRespose = parent::indexAction($request);

        return $this->render('SyliusJobSchedulerBundle:JobLog:index.html.twig', array(
            'listContent' => $contentRespose,
            'job'         => $job,
        ));
    }

    /**
     * Displays log output
     *
     * @param $logId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeOutputAction($logId)
    {
        $log = $this->get('sylius.repository.job_log')->find($logId);

        return $this->render('SyliusJobSchedulerBundle:JobLog:see-output.html.twig', array(
            'log' => $log,
        ));
    }

}
