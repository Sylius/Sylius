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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<CountryInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
    ) {
    }

    /**
     * @param CountryInterface $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, CountryInterface::class);
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        if (!$this->countryProvincesDeletionChecker->isDeletable($data)) {
            throw new ResourceDeleteException(message: 'Cannot delete, the province is in use.');
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
