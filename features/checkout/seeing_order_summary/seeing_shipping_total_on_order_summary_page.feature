@checkout
Feature: Seeing order shipping total on order summary page
    In order be certain about shipping total
    As a Customer
    I want to be able to see shipping total on order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Guards! Guards!" configurable product
        And this product has "Guards! Guards! - book" variant priced at "$20.00"
        And this product has "Guards! Guards! - ebook" variant priced at "$15.00" which does not require shipping
        And the store has "UPS" shipping method with "$20.00" fee
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing the shipping total on order summary
        Given I have "Guards! Guards! - book" variant of product "Guards! Guards!" in the cart
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "UPS" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order shipping should be "$20.00"

    @ui
    Scenario: Seeing the shipping total on order summary with discounted price
        Given there is a promotion "Holiday promotion"
        And the promotion gives "50%" discount on shipping to every order
        And I have "Guards! Guards! - book" variant of product "Guards! Guards!" in the cart
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "UPS" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order shipping should be "$10.00"

    @ui
    Scenario: Not seeing the shipping total on order summary if none of the order items require shipping
        Given I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I choose "Offline" payment method
        Then I should be on the checkout summary step
        And I should not see shipping total
