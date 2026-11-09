@worker @worker:currency @mink:workerphp
Feature: Currency isolation between consecutive requests
    In order to see prices in the currency of the shop I am on
    As a Customer
    I want the currency of the channel served before me not to stick

    The currency context is resolved per request and cached for its duration.
    In worker mode the service outlives the request, so a cache that is not
    cleared hands the first channel's currencies to the second. Both visits
    live in ONE scenario on purpose - the leak needs a predecessor.

    Background:
        Given the store operates on a channel named "Web" in "EUR" currency and with hostname "web.localhost.example"
        And that channel allows to shop using "EUR", "USD" and "GBP" currencies
        And the store operates on another channel named "Mobile" in "USD" currency and with hostname "m.localhost.example"
        And that channel allows to shop using "USD" and "GBP" currencies

    @no-api @ui
    Scenario: A channel served after another one offers only its own currencies
        When I browse the "Web" channel
        Then I should shop using the "EUR" currency
        When I browse the "Mobile" channel
        Then I should shop using the "USD" currency
        And I should not be able to shop using the "EUR" currency

    @no-api @ui
    Scenario: The first channel keeps its own currency after the second one was served
        When I browse the "Mobile" channel
        Then I should shop using the "USD" currency
        When I browse the "Web" channel
        Then I should shop using the "EUR" currency
        And I should be able to shop using the "GBP" currency

    @no-api @ui
    Scenario: Switching currency on one channel does not affect the default currency of another channel
        When I browse the "Web" channel
        And I switch to the "GBP" currency
        Then I should shop using the "GBP" currency
        When I browse the "Mobile" channel
        Then I should shop using the "USD" currency
        And I should not be able to shop using the "EUR" currency
