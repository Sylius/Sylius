@products
Feature: Product attribute groups
    In order to manage attributes more efficiently
    As a store owner
    I want to be able to group them

    Background:
        Given there is default currency configured
        And I am logged in as administrator
        And there are following attribute groups:
            | code      | name      |
            | marketing | Marketing |
            | technical | Technical |
        And there are following attributes:
            | name               | presentation       | group     |
            | container_material | Container material | technical |
            | release_date       | Release date       | marketing |

    Scenario: Seeing index of all attribute groups
        Given I am on the attribute index page
        When I follow "Configure groups"
        Then I should be on the product attribute group index page
        And I should see 2 groups in the list

    Scenario: Seeing empty index of attribute groups
        Given there are no product attribute groups
        When I am on the product attribute index page
        Then I should see "There are no attributes configured"

    Scenario: Submitting form without required fields
        Given I am on the product attribute group creation page
        When I press "Create"
        Then I should still be on the product attribute group creation page
        And I should see "Please enter attribute group code"
        And I should see "Please enter attribute group name"

    Scenario: Creating new attribute group
        Given I am on the product attribute group creation page
        When I fill in "Code" with "design"
        And I fill in "Name" with "Design"
        And I press "Create"
        Then I should be on the product attribute group index page
        And I should see "Attribute group has been successfully created."
        And I should see 3 attributes in the list

    Scenario: Updating the attribute group
        Given I am on the product attribute group index page
        And I click "edit" near "Marketing"
        When I fill in "Name" with "Marketing Details"
        And I press "Save changes"
        Then I should still be on the product attribute index page
        And I should see "Attribute has been successfully updated."

    @javascript
    Scenario: Deleted attribute disappears from the list
        Given I am on the product attribute group index page
        When I click "delete" near "Marketing"
        And I click "delete" from the confirmation modal
        Then I should still be on the product attribute group index page
        And I should see "Attribute group has been successfully deleted."
        And I should not see attribute group with name "Marketing" in that list
