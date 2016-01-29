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
    /**
     * @var string
     */
    private $url = 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';

    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * {@inheritdoc}
     */
    public function configure(array $options = [])
    {
        if (!isset($options['base_currency'])) {
            throw new \InvalidArgumentException('"base_currency" must be set in order to use EuropeanCentralBankImporter.');
        }

        $this->baseCurrency = $options['base_currency'];
    }

    /**
     * {@inheritdoc}
     */
    public function import(array $managedCurrencies = [])
    {
        $xml = @simplexml_load_file($this->url);
        if ($xml instanceof \SimpleXMLElement) {
            // base currency: euro
            $this->updateOrCreate($managedCurrencies, $this->baseCurrency, 1.00);

            $data = $xml->xpath('//gesmes:Envelope/*[3]/*');
            foreach ($data[0]->children() as $child) {
                $this->updateOrCreate($managedCurrencies, (string) $child->attributes()->currency, (float) $child->attributes()->rate);
            }

            $this->manager->flush();
        }
    }
}
