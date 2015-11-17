<?php

namespace Sylius\Bundle\CoreBundle\Purger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface QueryLoggerInterface extends SQLLogger
{
    /**
     * @return array
     */
    public function getLoggedQueries();

    /**
     * @return void
     */
    public function clearLoggedQueries();
}
