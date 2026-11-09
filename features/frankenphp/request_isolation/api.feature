@worker @api
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

    Scenario: Products from the API on both channels
        When I browse the "Channel A" channel
        And I browse products
        Then I should see the product "PHP T-Shirt"
        And I should not see the product "Symfony Mug"
        When I browse the "Channel B" channel
        And I browse products
        Then I should see the product "Symfony Mug"
        And I should not see the product "PHP T-Shirt"
        When I browse the "Channel A" channel
        And I browse products
        Then I should see the product "PHP T-Shirt"
        And I should not see the product "Symfony Mug"

    Scenario: Creating a cart via the API negotiates the channel's own locale
        When I browse the "Channel A" channel
        And I pick up cart in the "English (United States)" locale
        Then my cart's locale should be "English (United States)"
        When I browse the "Channel B" channel
        And I pick up cart in the "Polish (Poland)" locale
        Then my cart's locale should be "Polish (Poland)"

    Scenario: A shop cart does not crash a subsequent JWT API order request
        Given customer "customer@example.com" has placed 1 orders on the "Channel A" channel in each buying 1 "PHP T-Shirt" product
        When I browse the "Channel A" channel
        And I pick up cart in the "English (United States)" locale
        And I log in as "customer@example.com" with "sylius" password
        And I browse my orders
        Then I should see a single order in the list

    Scenario: A JWT cart after an anonymous shop visit does not leak to the next API client
        When I browse the "Channel A" channel
        And I pick up cart in the "English (United States)" locale
        And I log in as "customer@example.com" with "sylius" password
        And I pick up cart in the "English (United States)" locale
        And I log out
        When I browse the "Channel B" channel
        Then I should not have access to the summary of my previous cart
