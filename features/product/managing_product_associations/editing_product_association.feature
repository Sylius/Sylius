@managing_product_associations
Feature: Editing a product association
    In order to change information about a product association
    As an Administrator
    I want to be able to edit the product association

    Background:
        Given the store has a product association type "Blue"
        And the store has a product "Blue jean"
        And the store has a product "Blue tee-shirt"
        And the store has a product "Blue jacket"
        And the product "Blue jean" has an association "Blue" with product "Blue tee-shirt"
        And I am logged in as an administrator

    @ui @api
    Scenario: Changing the associated products
        When I want to modify the association with product owner "blue_jean" and association type "blue"
        And I want to associate the product "blue_jacket"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And The association with product owner "blue_jean" and association type "blue" should have 1 associate product

    @ui @api
    Scenario: Changing the product owner
        When I want to modify the association with product owner "blue_jean" and association type "blue"
        And I want the product "blue_jacket" to be the source
        And I save my changes
        Then I should be notified that it has been successfully edited
        And We should find the association with product owner "blue_jacket" and association type "blue"
