@checkout
Feature: Having prefilled registration form after checkout
    In order to make future purchases with ease
    As an Visitor
    I want to have prefilled account registration form after placing an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui @todo
    Scenario: Having prefilled registration form after checkout
        Given I have product "PHP T-Shirt" in the cart
        And I complete addressing step with email "john@example.com" and "United States" based shipping address
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        Then I should see the thank you page
        And I should see a registration button
        And this button should redirect to prefilled registration form

