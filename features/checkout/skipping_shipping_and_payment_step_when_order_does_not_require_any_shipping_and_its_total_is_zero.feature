@checkout
Feature: Skipping shipping and payment step when order does not require any shipping and its total is zero
    In order to not select shipping and payment method if its unnecessary
    As a Customer
    I want to be redirected directly to checkout summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Guards! Guards!" configurable product
        And this product has "Guards! Guards! - ebook" variant priced at "$12.55" which does not require shipping
        And this product has "Guards! Guards! - book" variant priced at "$22.55"
        And there is a promotion "Holiday promotion"
        And the promotion gives "$40.00" discount to every order with quantity at least 1
        And I am a logged in customer

    @ui
    Scenario: Seeing order summary page after addressing if none of order items require shipping and order total is zero
        Given I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout summary step
