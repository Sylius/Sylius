@checkout
Feature: Changing email during checkout with registered email
    In order to change email during checkout
    As a Customer
    I want to see email input field when im not logged in

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mantis blade" priced at "$1,200.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer "John Doe" identified by an email "john@example.com" and a password "secret"

    @ui
    Scenario: Being able to change the email when checking out as a guest
        Given I have product "Mantis blade" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I go back to addressing step of the checkout
        And I complete addressing step with email "new-email@example.com" and "United States" based billing address
        Then I should be checking out as "new-email@example.com"

    @ui
    Scenario: Being unable to change the email when checking out as a logged in user
        Given I am logged in as "john@example.com"
        And I have product "Mantis blade" in the cart
        When I complete addressing step with "United States" based billing address
        And I go to the addressing step
        Then I should not be able to change email
