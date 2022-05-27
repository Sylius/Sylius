@show_order @cli
Feature: Showing an order
    In order to be efficiently debug an order
    As a Developer
    I want to be be able to quickly show order information in the CLI

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00" in "United States" channel
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "john.doe@gmail.com" that placed an order "#00000025"

    Scenario: Show order information
        When I run show order command for order "#00000026"
        Then I should see the following information:
            | 1234 |
