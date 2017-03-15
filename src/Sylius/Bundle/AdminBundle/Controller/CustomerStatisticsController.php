<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CustomerStatisticsController
{
    /**
     * @var CustomerStatisticsProviderInterface
     */
    private $statisticsProvider;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param CustomerStatisticsProviderInterface $statisticsProvider
     * @param RepositoryInterface $customerRepository
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        CustomerStatisticsProviderInterface $statisticsProvider,
        RepositoryInterface $customerRepository,
        EngineInterface $templatingEngine
    ) {
        $this->statisticsProvider = $statisticsProvider;
        $this->customerRepository = $customerRepository;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function renderAction(Request $request)
    {
        $customerId = $request->query->get('customerId');

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                sprintf('Customer with id %s doesn\'t exist.', $customerId)
            );
        }

        $customerStatistics = $this->statisticsProvider->getCustomerStatistics($customer);

        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/Customer/Show/Statistics/index.html.twig',
            ['statistics' => $customerStatistics]
        );
    }
}
