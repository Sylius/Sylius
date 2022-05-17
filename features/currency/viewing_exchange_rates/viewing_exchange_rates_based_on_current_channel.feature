@viewing_exchange_rates
Feature: Viewing exchange rates based on current channel
    In order to be aware of exchange rates of the shop
    As a Customer
    I want to see all available for me exchange rates

Background:
    Given the store operates on a channel named "Web-US" with hostname "example.us"
    And the store also operates on a channel named "Web-UK" with hostname "example.uk"
    And "Web-US" channel allows to shop using "GBP" and "PLN" currencies
    And "Web-UK" channel allows to shop using the "BTN" currency
    And the exchange rate of "US Dollar" to "British Pound" is 0.7
    And the exchange rate of "US Dollar" to "Polish Zloty" is 4.44
    And the exchange rate of "British Pound" to "Bhutanese Ngultrum" is 2.37

@api
Scenario: Seeing exchange rates for currencies available in channel
    When I get exchange rates of the store
    Then I should see 2 exchange rates on the list
    And I should see that the exchange rate for "British Pound" is 0.7
