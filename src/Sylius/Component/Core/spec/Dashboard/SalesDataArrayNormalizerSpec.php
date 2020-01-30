<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\SalesDataArrayNormalizerInterface;

final class SalesDataArrayNormalizerSpec extends ObjectBehavior
{
    function it_implements_sales_data_array_normalizer_interface(): void
    {
        $this->shouldImplement(SalesDataArrayNormalizerInterface::class);
    }

    function it_does_nothing_if_array_has_data_for_all_months_in_given_period(): void
    {
        $this->completeNoSalesMonthData(
            new \DateTime('01-01-2019'),
            new \DateTime('31-12-2019'),
            ['01.19' => 10, '02.19' => 15, '03.19' => 20, '04.19' => 25, '05.19' => 122, '06.19' => 20, '07.19' => 12, '08.19' => 120, '09.19' => 150, '10.19' => 400, '11.19' => 300, '12.19' => 100]
        )->shouldReturn(
            ['01.19' => 10, '02.19' => 15, '03.19' => 20, '04.19' => 25, '05.19' => 122, '06.19' => 20, '07.19' => 12, '08.19' => 120, '09.19' => 150, '10.19' => 400, '11.19' => 300, '12.19' => 100]
        );
    }

    function it_completes_months_with_no_data(): void
    {
        $this->completeNoSalesMonthData(
            new \DateTime('01-01-2019'),
            new \DateTime('31-12-2019'),
            ['01.19' => 10, '03.19' => 20, '04.19' => 25, '05.19' => 122, '06.19' => 20, '08.19' => 120, '09.19' => 150, '11.19' => 300, '12.19' => 100]
        )->shouldReturn(
            ['01.19' => 10, '02.19' => 0, '03.19' => 20, '04.19' => 25, '05.19' => 122, '06.19' => 20, '07.19' => 0, '08.19' => 120, '09.19' => 150, '10.19' => 0, '11.19' => 300, '12.19' => 100]
        );
    }
}
