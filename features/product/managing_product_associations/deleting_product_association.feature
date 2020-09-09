@managing_product_associations
Feature: Deleting product association
    In order to remove test, obsolete or incorrect product association
    As an Administrator
    I want to be able to delete a product association

    Background:
        Given the store has a product association type "Blue"
        And the store has a product "Blue jean"
        And the store has a product "Blue tee-shirt"
        And the product "Blue jean" has an association "Blue" with product "Blue tee-shirt"
        And I am logged in as an administrator

    @ui @api
    Scenario: Deleting a product association
        When I want to delete the association with product owner "blue_jean" and association type "blue"
        Then I should be notified that it has been successfully deleted
