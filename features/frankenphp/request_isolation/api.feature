@worker @worker:api @api
Feature: Shop API request isolation
    In order to ensure API requests remain properly isolated in worker mode
    As an API client
    I want requests across channels, locales, and customers to never leak state

    Background:
        Given the store operates on a channel named "Channel A" with hostname "channel-a.localhost"
        And that channel allows to shop using the "English (United States)" locale
        And the store operates on another channel named "Channel B" with hostname "channel-b.localhost"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a product "PHP T-Shirt" priced at "$20.00" in "Channel A" channel
        And the store has a product "Symfony Mug" priced at "$15.00" in "Channel B" channel
        And there is a user "customer@example.com" identified by "sylius"

    Scenario: Creating a cart via the API negotiates the channel's own locale
        When I browse the "Channel A" channel
        And I pick up cart in the "English (United States)" locale
        Then my cart's locale should be "English (United States)"
        When I browse the "Channel B" channel
        And I pick up cart in the "Polish (Poland)" locale
        Then my cart's locale should be "Polish (Poland)"
