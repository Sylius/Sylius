<?php

namespace spec\Sylius\Bundle\CoreBundle\Purger;

use Doctrine\DBAL\Logging\SQLLogger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Purger\QueryLoggerInterface;

/**
 * @mixin \Sylius\Bundle\CoreBundle\Purger\QueryLogger
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class QueryLoggerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Purger\QueryLogger');
    }

    function it_implements_Query_Logger_interface()
    {
        $this->shouldImplement(QueryLoggerInterface::class);
    }

    function it_implements_Doctrine_SQL_Logger_interface()
    {
        $this->shouldImplement(SQLLogger::class);
    }

    function it_saves_runned_queries()
    {
        $this->startQuery('SQL QUERY', ['param' => 'value'], ['types?']);

        $this->getLoggedQueries()->shouldReturn([
            [
                'sql' => 'SQL QUERY',
                'params' => ['param' => 'value'],
                'types' => ['types?'],
            ],
        ]);
    }

    function it_clears_saved_queries()
    {
        $this->startQuery('SQL QUERY', ['param' => 'value'], ['types?']);

        $this->clearLoggedQueries();
        $this->getLoggedQueries()->shouldReturn([]);
    }
}
