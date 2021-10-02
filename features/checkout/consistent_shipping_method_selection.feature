@checkout
Feature: Selected shipping method stays consistent throughout checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a tax category "tax category"
        And the store has "VAT" tax rate of 20% for "tax cageroy" within the "United States" zone
        And the store has a product "Mantis blade" priced at "$91"
        And the product "Mantis blade" belongs to "tax category" tax category
        And the store has "A" shipping method with "$10" fee
        And this shipping method is only available for orders under or equal to "$100"
        And the store has "B" shipping method with "$0" fee
        And this shipping method is only available for orders over or equal to "$100"
        And the store allows paying offline
        And there is a customer "John Doe" identified by an email "john@example.com" and a password "secret"

    @ui
    Scenario: Method A should be available and not method B
        Given I have product "Mantis blade" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        Then I should see "A" shipping method
        And I should not see "B" shipping method

    @ui
    Scenario: Selected shipping method stays consistent throughout checkout
        Given I have product "Mantis blade" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I completed the shipping step with "A" shipping method
        And I choose "Offline" payment method
        Then my order's shipping method should be "A"
