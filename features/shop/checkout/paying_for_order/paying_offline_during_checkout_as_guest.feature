@paying_for_order
Feature: Paying Offline during checkout as guest
    In order to pay with cash or by external means
    As a Guest
    I want to be able to complete checkout process without paying

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "French (France)" locales
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline

    @ui @api
    Scenario: Successfully placing an order
        Given I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I proceed with "Free" shipping method
        And I choose "Offline" payment method
        And I confirm my order
        Then I should see the thank you page

    @ui @no-api
    Scenario: Successfully placing an order using custom locale
        Given I have product "PHP T-Shirt" in the cart
        When I proceed through checkout process in the "French (France)" locale with email "john@example.com"
        And I confirm my order
        Then I should see the thank you page in "French (France)"

    @api @no-ui
    Scenario: Successfully placing an order using custom locale
        Given I pick up cart in the "French (France)" locale
        And I add product "PHP T-Shirt" to the cart
        When I proceed through checkout process
        And I confirm my order
        Then my order's locale should be "French (France)"
