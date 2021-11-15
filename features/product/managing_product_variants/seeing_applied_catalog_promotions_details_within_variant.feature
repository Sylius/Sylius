@viewing_products
Feature: Seeing applied catalog promotions details within variant
    In order to be aware of variant's price change reason
    As an Administrator
    I want to see details of catalog promotion nearby variant's price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Lemon" variant priced at "$10.00"
        And there is a catalog promotion "Winter sale" available in "United States" channel that reduces price by "50%" and applies on "Wyborowa Vodka Exquisite" variant
        And I am logged in as an administrator

    @ui
    Scenario: Seeing applied catalog promotion details within variant
        When I access "Wyborowa Vodka" product
        Then "Wyborowa Vodka Exquisite" variant price should be decreased by catalog promotion "Winter sale"
