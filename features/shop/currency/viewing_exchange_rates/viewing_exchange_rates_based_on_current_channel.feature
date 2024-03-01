@viewing_exchange_rates
Feature: Viewing exchange rates based on current channel
    In order to be aware of exchange rates of the shop
    As a Customer
    I want to see all available for me exchange rates

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency and with hostname "example.us"
        And the store also operates on a channel named "Web-UK" in "GBP" currency and with hostname "example.uk"
        And "Web-US" channel allows to shop using "GBP" and "PLN" currencies
        And "Web-UK" channel allows to shop using the "BTN" currency
        And the exchange rate of "US Dollar" to "British Pound" is 0.7
        And the exchange rate of "Polish Zloty" to "US Dollar" is 0.23
        And the exchange rate of "British Pound" to "Bhutanese Ngultrum" is 2.37

    @api
    Scenario: Seeing exchange rates for currencies available in channel
        Given I changed my current channel to "Web-US"
        When I get exchange rates of the store
        Then I should see 2 exchange rates on the list
        And I should see that the exchange rate of "US Dollar" to "British Pound" is 0.7
        And I should see that the exchange rate of "Polish Zloty" to "US Dollar" is 0.23
        And I should not see "British Pound" to "Bhutanese Ngultrum" exchange rate
