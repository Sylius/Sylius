<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultCustomerGroupsStoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultCustomerGroupsStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_customer_groups(): void
    {
        /** @var DefaultCustomerGroupsStoryInterface $defaultCustomerGroupsStory */
        $defaultCustomerGroupsStory = self::getContainer()->get('sylius.data_fixtures.story.default_customer_groups');

        $defaultCustomerGroupsStory->build();

        $customerGroup = $this->getCustomerGroupByCode('retail');
        $this->assertNotNull($customerGroup, sprintf('Customer group "%s" was not found but it should.', 'retail'));
        $this->assertEquals('Retail', $customerGroup->getName());

        $customerGroup = $this->getCustomerGroupByCode('wholesale');
        $this->assertNotNull($customerGroup, sprintf('Customer group "%s" was not found but it should.', 'wholesale'));
        $this->assertEquals('Wholesale', $customerGroup->getName());
    }

    private function getCustomerGroupByCode(string $code): ?CustomerGroupInterface
    {
        /** @var RepositoryInterface $customerGroupRepository */
        $customerGroupRepository = self::getContainer()->get('sylius.repository.customer_group');

        return $customerGroupRepository->findOneBy(['code' => $code]);
    }
}
