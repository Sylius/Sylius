@worker @worker:channel @mink:workerphp
Feature: Channel isolation between consecutive requests
    In order to serve several channels from one long-lived process
    As a Developer
    I want a request to see its own channel, not the one served before it

    In worker mode a single PHP process answers one request after another, so a
    shared service that remembers the first channel hands it to every request
    that follows. Each scenario below is a SEQUENCE of requests inside one
    scenario, with the assertion after the second one - split across two
    scenarios it would measure nothing, because the leak needs a predecessor.

    Background:
        Given the store operates on a channel named "Poland"
        And the store has a product "Onion" priced at "$10.00" in "Poland" channel
        And the store operates on another channel named "United States"
        And the store has a product "Banana" priced at "$5.00" in "United States" channel

    @no-api @ui
    Scenario: A channel served after another one still sees only its own products
        When I browse the "Poland" channel
        Then I should be able to access product "Onion"
        When I browse the "United States" channel
        Then I should be able to access product "Banana"
        But I should not be able to access product "Onion"

    @no-api @ui
    Scenario: The first channel still sees its own products after the second one was served
        When I browse the "United States" channel
        Then I should be able to access product "Banana"
        When I browse the "Poland" channel
        Then I should be able to access product "Onion"
        But I should not be able to access product "Banana"

    @no-api @ui
    Scenario: A channel returned to after the other one still sees only its own products
        When I browse the "Poland" channel
        Then I should be able to access product "Onion"
        When I browse the "United States" channel
        Then I should be able to access product "Banana"
        When I browse the "Poland" channel
        Then I should be able to access product "Onion"
        But I should not be able to access product "Banana"
