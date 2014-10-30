<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Importer;

class EuropeanCentralBankImporter extends AbstractImporter
{
    private $url = 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';

    /**
     * {@inheritdoc}
     */
    public function configure(array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function import(array $managedCurrencies = array())
    {
        $xml = @simplexml_load_file($this->url);
        if ($xml instanceof \SimpleXMLElement) {
            $data = $xml->xpath('//gesmes:Envelope/*[3]/*');
            foreach ($data[0]->children() as $child) {
                $this->updateOrCreate($managedCurrencies, (string) $child->attributes()->currency, (float) $child->attributes()->rate);
            }

            $this->manager->flush();
        }
    }
}
