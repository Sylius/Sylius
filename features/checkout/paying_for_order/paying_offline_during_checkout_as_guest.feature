@paying_for_order
Feature: Paying offline during checkout as guest
    In order to pay with cash or by external means
    As a Guest
    I want to be able to complete checkout process without paying

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "French (France)" locales
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Successfully placing an order
        Given I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based shipping address
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Successfully placing an order using custom locale
        Given I have product "PHP T-Shirt" in the cart
        When I proceed through checkout process in the "French (France)" locale
        And I confirm my order
        Then I should see the thank you page in "French (France)"
