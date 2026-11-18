@worker @worker:currency @mink:workerphp
Feature: A price is converted with the current exchange rate

    CurrencyConverter keeps the rates it looked up in a property, so a worker
    converts every later price with whatever it found while answering an
    earlier request. Both conversions below happen in ONE scenario on purpose -
    the stale rate only shows once something has already been converted.

    @no-api @ui
    Scenario: A rate changed after the first conversion is used by the next one
        Given the store operates on a channel named "Web" in "USD" currency
        And that channel allows to shop using "USD" and "GBP" currencies
        And the exchange rate of "USD" to "GBP" is 2.00
        And the store has a product "Mug" priced at "$10.00"
        When I switch to the "GBP" currency
        And I view product "Mug"
        Then I should see the product price "£20.00"
        Given the exchange rate of "USD" to "GBP" changes to 4.00
        When I view product "Mug"
        Then I should see the product price "£40.00"
