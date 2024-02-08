<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

final class CustomerStatisticsController
{
    public function __construct(
        private CustomerStatisticsProviderInterface $statisticsProvider,
        private RepositoryInterface $customerRepository,
        private Environment $templatingEngine,
    ) {
    }

    /**
     * @throws HttpException
     */
    public function renderAction(Request $request): Response
    {
        $customerId = $request->query->get('customerId');

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                sprintf('Customer with id %s doesn\'t exist.', (string) $customerId),
            );
        }

        $customerStatistics = $this->statisticsProvider->getCustomerStatistics($customer);

        return new Response($this->templatingEngine->render(
            '@SyliusAdmin/Customer/Show/Statistics/index.html.twig',
            ['statistics' => $customerStatistics],
        ));
    }
}
