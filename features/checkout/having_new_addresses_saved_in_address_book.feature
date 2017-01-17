@checkout
Feature: Having new addresses saved in the address book after checkout
    In order to ease my address management
    As a Customer
    I want new addresses provided during checkout to be saved in my address book

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I have product "Lannister Coat" in the cart

    @ui
    Scenario: Having the shipping address saved in my address book
        Given I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        Then I should have a single address in my address book

    @ui
    Scenario: Having the shipping and billing addresses saved in my address book
        Given I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the billing address as "Pseudopolis", "Haggard", "00-007", "United States" for "Sarah Connor"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        Then I should have 2 addresses in my address book

    @ui
    Scenario: Addresses already existent in my book don't get saved
        Given I have an address "Jon Snow", "Frost Alley", "90210", "Ankh Morpork", "United States" in my address book
        And I have an address "Sarah Connor", "Haggard", "00-007", "Pseudopolis", "United States" in my address book
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the billing address as "Pseudopolis", "Haggard", "00-007", "United States" for "Sarah Connor"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        Then I should still have 2 addresses in my address book
