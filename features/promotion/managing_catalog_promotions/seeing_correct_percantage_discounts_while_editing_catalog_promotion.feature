@managing_catalog_promotions
Feature: Seeing correct percentage discounts while editing catalog promotion
    In order to see the accurate percentage amount while editing the catalog promotion
    As a store owner
    I want to see the correct percentage amount while editing the catalog promotion with decimal places

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And there is a catalog promotion with "christmas_sale" code and "Christmas sale" name
        And it applies on "PHP T-Shirt" variant
        And it reduces price by "30%"
        And it is enabled
        And I am logged in as an administrator

    @api @ui @javascript
    Scenario: Seeing the accurate percentage amount after editing the catalog promotion including the value up to one decimal place
        When I edit "Christmas sale" catalog promotion to have "2.5%" discount
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this catalog promotion should have "2.50%" percentage discount

    @api @ui @javascript
    Scenario: Seeing the accurate percentage amount after editing the catalog promotion including the value up to two decimal places
        When I edit "Christmas sale" catalog promotion to have "2.56%" discount
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this catalog promotion should have "2.56%" percentage discount
