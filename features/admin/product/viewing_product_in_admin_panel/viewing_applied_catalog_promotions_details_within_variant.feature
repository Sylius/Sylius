@viewing_product_in_admin_panel
Feature: Seeing applied catalog promotions details within variant
    In order to be aware of variant's price change reason
    As an Administrator
    I want to see details of catalog promotion nearby variant's price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Lemon" variant priced at "$10.00"
        And there is a catalog promotion with "winter_sale" code and "Winter sale" name
        And the catalog promotion "Winter sale" is available in "United States"
        And it applies on "Wyborowa Vodka Exquisite" variant
        And it reduces price by "13%"
        And it is enabled
        And there is also a catalog promotion with "christmas_sale" code and "Christmas sale" name
        And the catalog promotion "Christmas sale" is available in "United States"
        And it applies on "Wyborowa Vodka Lemon" variant
        And it reduces price by "37%"
        And it is enabled
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Seeing applied catalog promotion details within variant
        When I access "Wyborowa Vodka" product
        Then "Wyborowa Vodka Exquisite" variant price should be decreased by catalog promotion "Winter sale" in "United States" channel
        And "Wyborowa Vodka Lemon" variant price should not be decreased by catalog promotion "Winter sale" in "United States" channel
        And "Wyborowa Vodka Exquisite" variant price should not be decreased by catalog promotion "Christmas sale" in "United States" channel
        And "Wyborowa Vodka Lemon" variant price should be decreased by catalog promotion "Christmas sale" in "United States" channel

    @api @no-ui
    Scenario: Seeing applied catalog promotion details within variant
        When I view all variants of the product "Wyborowa Vodka"
        Then "Wyborowa Vodka Exquisite" variant price should be decreased by catalog promotion "Winter sale" in "United States" channel
        And "Wyborowa Vodka Lemon" variant price should not be decreased by catalog promotion "Winter sale" in "United States" channel
        And "Wyborowa Vodka Exquisite" variant price should not be decreased by catalog promotion "Christmas sale" in "United States" channel
        And "Wyborowa Vodka Lemon" variant price should be decreased by catalog promotion "Christmas sale" in "United States" channel
