@managing_product_attributes
Feature: Deleting a product attribute
    In order to keep my collection of product attributes not cluttered
    As an administrator
    I want to be able to remove an attribute that is not assigned to any of the products

    Background:
        Given I am logged in as an administrator
        And the store has a product "44 Magnum"

    @ui @api
    Scenario: Trying to delete an attribute from the registry when a product uses it
        Given this product has a text attribute "Gun caliber" with value "11 mm"
        When I delete this product attribute
        Then I should be notified that it is in use

    @ui @api
    Scenario: Deleting a text product attribute when it's not used
        Given the store has a text product attribute "Gun caliber"
        When I delete this product attribute
        Then I should be notified that it has been successfully deleted
        And this product attribute should no longer exist in the registry
