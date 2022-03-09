@managing_catalog_promotions
Feature: Prioritizing a catalog promotion
    In order to avoid the same catalog promotion priority
    As an Administrator
    I want to set distinct priority on multiple catalog promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Winter sale" with priority 20
        And there is a catalog promotion "Year-end sale" with priority 30
        And there is a catalog promotion "Spring sale" with priority 40
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a catalog promotion with a priority higher than all existing catalog promotions does not change priority values
        When I create a new catalog promotion with "collection_sale" code and "Collection sale" name and 50 priority
        Then there should be 4 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 20
        And the catalog promotion named "Year-end sale" should have priority 30
        And the catalog promotion named "Spring sale" should have priority 40
        And the catalog promotion named "Collection sale" should have priority 50

    @api @ui
    Scenario: Adding a catalog promotion with a priority lower than all existing ones
              increases the priority value of other catalog promotions by 1
        When I create a new catalog promotion with "collection_sale" code and "Collection sale" name and 10 priority
        Then there should be 4 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 21
        And the catalog promotion named "Year-end sale" should have priority 31
        And the catalog promotion named "Spring sale" should have priority 41
        And the catalog promotion named "Collection sale" should have priority 10

    @api @ui
    Scenario: Adding a catalog promotion with a priority equal to one of the existing catalog promotions
              increases the priority value of all catalog promotions with a priority greater equal than created catalog promotion by 1,
              but has no effect on the others
        When I create a new catalog promotion with "collection_sale" code and "Collection sale" name and 30 priority
        Then there should be 4 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 20
        And the catalog promotion named "Year-end sale" should have priority 31
        And the catalog promotion named "Spring sale" should have priority 41
        And the catalog promotion named "Collection sale" should have priority 30

    @api @ui
    Scenario: Adding a catalog promotion with a priority equal -1
              sets a priority value of the created promotion one greater than the current highest value
        When I create a new catalog promotion with "collection_sale" code and "Collection sale" name and -1 priority
        Then there should be 4 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 20
        And the catalog promotion named "Year-end sale" should have priority 30
        And the catalog promotion named "Spring sale" should have priority 40
        And the catalog promotion named "Collection sale" should have priority 41

    @api @ui
    Scenario: Adding a catalog promotion with some negative priority lower than -1
              determines the position of the created catalog promotion starting count backward and if calculated index
              is already taken increases the priority value of all catalog promotions with a priority greater equal than
              calculated priority value by 1
        When I create a new catalog promotion with "collection_sale" code and "Collection sale" name and -15 priority
        Then there should be 4 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 20
        And the catalog promotion named "Year-end sale" should have priority 31
        And the catalog promotion named "Spring sale" should have priority 41
        And the catalog promotion named "Collection sale" should have priority 27

    @api @ui
    Scenario: Updating a catalog promotion priority to one of the existing catalog promotions decreases its priority value by 1
        When I want to modify a catalog promotion "Winter sale"
        And I set its priority to 30
        And I save my changes
        Then there should be 3 catalog promotions on the list
        And the catalog promotion named "Winter sale" should have priority 30
        And the catalog promotion named "Year-end sale" should have priority 29
        And the catalog promotion named "Spring sale" should have priority 40
