@legacy @product
Feature: Products
    In order to create my offer
    As a store owner
    I want to be able to manage products

    Background:
        Given store has default configuration
        And there are following options:
            | code | name          | values                          |
            | O1   | T-Shirt color | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | S[OV4], M[OV5], L[OV6]          |
        And there are following attributes:
            | name               | type     | configuration |
            | T-Shirt fabric     | text     | min:2,max:255 |
            | T-Shirt fare trade | checkbox |               |
            | Size               | integer  |               |
        And the following products exist:
            | name          | price | options | attributes            |
            | Super T-Shirt | 19.99 | O2, O1  | T-Shirt fabric:Wool   |
            | Black T-Shirt | 19.99 | O1      | T-Shirt fabric:Cotton |
            | Mug           | 5.99  |         |                       |
            | Sticker       | 10.00 |         |                       |
        And there are following association types:
            | code | name       |
            | PAs1 | Cross sell |
            | PAs2 | Up sell    |
        And product "Super T-Shirt" is available in all variations
        And there are following tax categories:
            | code | name        |
            | TC1  | Clothing    |
            | TC2  | Electronics |
            | TC3  | Print       |
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
            | RTX2 | Special  |
        And taxon "Category" has following children:
            | Clothing[TX1] > T-Shirts[TX2]         |
            | Clothing[TX1] > Premium T-Shirts[TX3] |
        And taxon "Special" has following children:
            | Featured[TX4] |
            | New[TX5]      |
        And product "Sticker" has main taxon "New"
        And I am logged in as administrator

    Scenario: Seeing index of all products with simple prices
        Given I am on the dashboard page
        When I follow "Products"
        Then I should be on the product index page
        And I should see 4 products in the list
        And I should see product with retail price "€5.99" in that list
        But I should not see product with retail price "€19.99" in that list

    Scenario: Seeing empty index of products
        Given there are no products
        When I am on the product index page
        Then I should see "There are no products to display"

    Scenario: Submitting form without specifying the required values
        Given I am on the product index page
        And I follow "Create product"
        When I press "Create"
        Then I should still be on the product creation page
        And I should see "Please enter product name"

    Scenario: Creating simple product without any attributes and options
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Book about Everything   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And I press "Create"
        Then I should be on the page of product "Book about Everything"
        And I should see "Product has been successfully created"

    Scenario: Prices are saved correctly
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Book about Everything   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And I press "Create"
        Then I should be on the page of product "Book about Everything"
        And I should see "Product has been successfully created"

    Scenario: Creating product with options
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Manchester United tee   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And I select "T-Shirt size" from "Options"
        And I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And "Product has been successfully created" should appear on the page
        And I should see "T-Shirt size"

    @javascript
    Scenario: Creating product with association
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Manchester United tee   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And go to "Association" tab
        And I click "Add association"
        And I select "Up sell" from "Association type"
        And I select "Mug" from "Associated product"
        And I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And "Product has been successfully created" should appear on the page
        And I should see "Up sell"
        And I should see "Mug"

    @javascript
    Scenario: Creating product with string attribute
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Manchester United tee   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And go to "Attributes" tab
        And I add "T-Shirt fabric" attribute
        And I fill in "T-Shirt fabric" with "Polyester"
        When I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And "Product has been successfully created" should appear on the page

    @javascript
    Scenario: Creating product with checkbox attribute
        Given I am on the product creation page
        When I fill in the following:
            | Name        | Manchester United tee   |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And go to "Attributes" tab
        And I add "T-Shirt fare trade" attribute
        And I check "T-Shirt fare trade"
        When I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And "Product has been successfully created" should appear on the page

    @javascript
    Scenario: Creating product with multiple attributes
        Given I am on the product creation page
        When I fill in the following:
            | Name        | FC Barcelona tee        |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And go to "Attributes" tab
        And I add following attributes:
            | T-Shirt fabric     |
            | T-Shirt fare trade |
        And I fill in "T-Shirt fabric" with "Polyester"
        And I check "T-Shirt fare trade"
        When I press "Create"
        Then I should be on the page of product "FC Barcelona tee"
        And "Product has been successfully created" should appear on the page
        And I should see "T-Shirt fabric"
        And I should see "T-Shirt fare trade"

    @javascript
    Scenario: Created product does not pass validation
        Given I am on the product creation page
        When I fill in the following:
            | Name        | FC Barcelona tee        |
            | Code        | TEE_CODE                |
            | Description | Interesting description |
        And I go to "Attributes" tab
        And I add "T-Shirt fabric" attribute
        And I fill in "T-Shirt fabric" with "X"
        When I press "Create"
        Then I should still be on the product creation page
        When I go to "Attributes" tab
        And I should see "This value is too short. It should have 2 characters or more"

    Scenario: Created products appear in the list
        Given I am on the product creation page
        And I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Code        | BOSTON_TEE              |
        And I press "Create"
        When I go to the product index page
        Then I should see 5 products in the list
        And I should see product with name "Manchester United tee" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the product index page
        When I click "Edit" near "Mug"
        Then I should be editing product "Mug"

    Scenario: Updating the product data
        Given I am editing product "Sticker"
        When I fill in "Name" with "Big Sticker"
        And I fill in "Description" with "This sticker is awesome"
        And I press "Save changes"
        Then I should be on the page of product "Big Sticker"
        And I should see "Product has been successfully updated"

    @javascript
    Scenario: Modifying product attributes
        Given I am editing product "Super T-Shirt"
        And I go to "Attributes" tab
        When I delete "T-Shirt fabric" attribute
        And I add "T-Shirt fare trade" attribute
        And I check "T-Shirt fare trade"
        And I press "Save changes"
        Then I should be on the page of product "Super T-Shirt"
        And I should see "T-Shirt fare trade"
        And I should not see "Wool"

    @javascript
    Scenario: Selecting the categorization taxons
        Given I am editing product "Black T-Shirt"
        And go to "Categorization" tab
        When I select "T-Shirts" from "Taxons"
        And I additionally select "Featured" from "Taxons"
        And I press "Save changes"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Product has been successfully updated"
        And "Featured" should appear on the page

    @javascript
    Scenario: Deleting product
        Given I am on the page of product "Mug"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the product index page
        And I should see "Product has been successfully deleted"
        And I should not see product with name "Mug" in that list

    Scenario: Accessing the product details page from list
        Given I am on the product index page
        When I click "Mug"
        Then I should be on the page of product "Mug"

    @javascript
    Scenario: Creating product with main taxon
        Given I am on the product creation page
        And I fill in the following:
            | Name        | The best T-shirt        |
            | Code        | BEST_T_SHIRT            |
            | Description | Interesting description |
        And go to "Categorization" tab
        And I select "New" from "Main taxon"
        And I press "Create"
        Then I should be on the page of product "The best T-shirt"
        And "Product has been successfully created" should appear on the page
        And "New" should appear on the page

    @javascript
    Scenario: Selecting the main taxon of product
        Given I am editing product "Black T-Shirt"
        And go to "Categorization" tab
        When I select "New" from "Main taxon"
        And I press "Save changes"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Product has been successfully updated"
        And "New" should appear on the page

    @javascript
    Scenario: Deleting product with main taxon
        Given I am on the page of product "Sticker"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the product index page
        And I should see "Product has been successfully deleted"
        And I should not see product with name "Sticker" in that list

    Scenario: Enabling product
        Given There is disabled product named "Mug"
        And I am on the page of product "Mug"
        When I press "Enable"
        Then I should be on the product index page
        And I should see "Product has been successfully enabled"

    Scenario: Disabling product
        Given There is enabled product named "Mug"
        And I am on the page of product "Mug"
        When I press "Disable"
        Then I should be on the product index page
        And I should see "Product has been successfully disabled"
