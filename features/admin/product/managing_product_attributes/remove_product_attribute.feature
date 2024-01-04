@managing_product_attributes
Feature:Removing a attribute
    In order to keep my collection of product attributes not cluttered
    As an administrator
    I want to be able to remove an attribute that is not assigned to any of the products

    Background:
        Given I am logged in as an administrator
        And the store has a product "44 Magnum"

    @ui
    Scenario: Try deleting a attribute from the registry when product use him
        Given this product has a text attribute "Gun caliber" with value "11 mm"
        When I delete this product attribute
        Then I should be notified that it has been failed deleted "product attribute"

    @ui
    Scenario: Deleting a text product attribute when not by used
        Given the store has a text product attribute "Gun caliber"
        When I delete this product attribute
        Then I should be notified that it has been successfully deleted
        And this product attribute should no longer exist in the registry
