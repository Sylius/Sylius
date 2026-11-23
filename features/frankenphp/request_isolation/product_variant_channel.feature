@worker @worker:product @mink:workerphp
Feature: A channel created during process runtime can be priced on a product variant

    ChannelCollectionType reads every channel once, in configureOptions(). That
    method runs once per ResolvedFormType, and the form registry keeps that
    instance for as long as the process lives, so the list is frozen at the
    first form built. A channel created afterwards gets no price field at all -
    no error, no warning, just a field that is not there.

    Both visits live in ONE scenario, because the leak needs a predecessor: the
    first visit is what freezes the list. The channel is created between them,
    the same way the currency scenario changes its rate mid-scenario.

    Background:
        Given the store operates on a channel named "Web-US"
        And the store has a product "T-Shirt"
        And the product "T-Shirt" has a "Small T-Shirt" variant priced at "$10.00"
        And I am logged in as an administrator

    @no-api @ui
    Scenario: A variant takes a price for a channel created after the first form was built
        When I want to modify the "Small T-Shirt" product variant
        Given the store operates on another channel named "Mobile"
        When I want to modify the "Small T-Shirt" product variant
        And I set its price to "$15.00" for "Mobile" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
