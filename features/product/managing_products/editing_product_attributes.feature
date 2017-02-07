@managing_products
Feature: Editing product's attributes
    In order to modify product details
    As an Administrator
    I want to be able to edit product's attributes

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "44 Magnum"
        And the store has a text product attribute "Overall length"
        And this product has text attribute "Gun caliber" with value "11 mm"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Seeing message about no new attributes selected
        When I want to modify the "44 Magnum" product
        And I try to add new attributes
        And I save my changes
        Then attribute "Gun caliber" of product "44 Magnum" should be "11 mm"
        And product "44 Magnum" should have 1 attribute

    @ui @javascript
    Scenario: Seeing message about no new attributes selected after all attributes deletion
        When I want to modify the "44 Magnum" product
        And I remove its "Gun caliber" attribute
        And I try to add new attributes
        And I save my changes
        Then product "44 Magnum" should not have any attributes
