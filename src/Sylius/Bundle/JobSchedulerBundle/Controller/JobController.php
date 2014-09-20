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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Job controller
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobController extends ResourceController
{
    /**
     * Runs a job asynchronously
     *
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function runAction(Request $request, $id)
    {
        $this->get('sylius.scheduler.job.manager')->runJobAsync($id);

        $session = $request->getSession();
        $session->getFlashBag()->add('success', $this->get('translator')->trans('sylius.job.message.job-has-run-successfully'));

        $response = $this->renderView('SyliusJobSchedulerBundle:Job:run.html.twig', array('id' => $id));

        return new Response($response);
    }

    /**
     * Returns if given job is running
     * (Used in ajax polling)
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function isRunningAction($id)
    {
        $job = $this->get('doctrine')->getRepository('SyliusJobSchedulerBundle:Job')->find($id);

        return new JsonResponse(json_encode($job->getIsRunning()));
    }


    /**
     * Called via ajax to update data row of a job in the list view
     *
     * @param $id
     *
     * @return Response
     */
    public function rowTemplateAction($id)
    {
        $jobRepository = $this->get('sylius.repository.job');
        $job           = $jobRepository->find($id);

        $response = $this->renderView('SyliusJobSchedulerBundle:Job:_macro.html.twig', array('job' => $job));

        return new Response($response);
    }
}
