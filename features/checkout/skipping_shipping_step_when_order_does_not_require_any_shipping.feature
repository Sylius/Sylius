@checkout
Feature: Skipping shipping step when order does not require any shipping
    In order to not select shipping method if its unnecessary
    As a Customer
    I want to be redirected directly to payment selection

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Guards! Guards!" configurable product
        And this product has "Guards! Guards! - book" variant priced at "$20.00"
        And this product has "Guards! Guards! - ebook" variant priced at "$12.55" which does not require shipping
        And the store has "SHL" shipping method with "$5.00" fee
        And I am a logged in customer

    @ui
    Scenario: Seeing checkout payment page after addressing if none of order items require shipping
        Given I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout payment step

    @ui
    Scenario: Seeing checkout shipping page after addressing if at least one of order items require shipping
        Given I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        And I have "Guards! Guards! - book" variant of product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
