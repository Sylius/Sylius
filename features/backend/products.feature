@products
Feature: Products
    In order to create my offer
    As a store owner
    I want to be able to manage products

    Background:
        Given I am logged in as administrator
          And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
          And there are following properties:
            | name               | presentation      | type     | choices   |
            | T-Shirt fabric     | T-Shirt           | text     |           |
            | T-Shirt fare trade | Faretrade product | checkbox |           |
            | Color              | color             | choice   | red, blue |
            | Size               | size              | number   |           |
          And the following products exist:
            | name          | price | options                     | properties             |
            | Super T-Shirt | 19.99 | T-Shirt size, T-Shirt color | T-Shirt fabric: Wool   |
            | Black T-Shirt | 19.99 | T-Shirt size                | T-Shirt fabric: Cotton |
            | Mug           | 5.99  |                             |                        |
            | Sticker       | 10.00 |                             |                        |
          And product "Super T-Shirt" is available in all variations
          And there are following tax categories:
            | name        |
            | Clothing    |
            | Electronics |
            | Print       |
          And there are following taxonomies defined:
            | name     |
            | Category |
            | Special  |
          And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts         |
            | Clothing > Premium T-Shirts |
          And taxonomy "Special" has following taxons:
            | Featured |
            | New      |

    Scenario: Seeing index of all products
        Given I am on the dashboard page
         When I follow "Products"
         Then I should be on the product index page
          And I should see 4 products in the list

    Scenario: Listing only simple product prices
        Given I am on the product index page
         Then I should see product with price "€5.99" in that list
          But I should not see product with price "€19.99" in that list

    Scenario: Seeing empty index of products
        Given there are no products
         When I am on the product index page
         Then I should see "There are no products to display"

    Scenario: Accessing the product creation form
        Given I am on the dashboard page
         When I follow "Products"
          And I follow "Create product"
         Then I should be on the product creation page

    Scenario: Submitting form without specifying the name
        Given I am on the product creation page
         When I press "Create"
         Then I should still be on the product creation page
          And I should see "Please enter product name."

    Scenario: Trying to create product without description
        Given I am on the product creation page
         When I fill in "Name" with "Bag"
          And I press "Create"
         Then I should still be on the product creation page
          And I should see "Please enter product description."

    Scenario: Trying to create product without the price
        Given I am on the product creation page
         When I fill in "Name" with "Bag"
          And I press "Create"
         Then I should still be on the product creation page
          And I should see "Please enter the price."

    Scenario: Trying to create product with invalid price
        Given I am on the product creation page
         When I fill in "Name" with "Bag"
          And I fill in "Price" with "-0.01"
          And I press "Create"
         Then I should still be on the product creation page
          And I should see "Price must not be negative."

    Scenario: Creating simple product without any properties and options
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Book about Everything   |
            | Description | Interesting description |
            | Price       | 29.99                   |
          And I press "Create"
         Then I should be on the page of product "Book about Everything"
          And I should see "Product has been successfully created."

    Scenario: Creating product with options
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And I select "T-Shirt size" from "Options"
          And I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page
          And I should see "T-Shirt size"

    @javascript
    Scenario: Specifying the variant selection method
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Book about Everything   |
            | Description | Interesting description |
            | Price       | 29.99                   |
          And go to "Options" tab
          And I select "T-Shirt size" from "Options"
          And I select "Options matching" from "Variant selection method"
          And I press "Create"
         Then I should be on the page of product "Book about Everything"
          And I should see "Product has been successfully created."
          And "Options matching" should appear on the page

    @javascript
    Scenario: Creating product with string property
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And go to "Properties" tab
          And I click "Add property"
          And I select "T-Shirt fabric" from "Property"
          And I fill in "Value" with "Cotton"
          And I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page
          And I should see "Cotton"

    @javascript
    Scenario: Creating product with boolean property
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And go to "Properties" tab
          And I click "Add property"
          And I select "T-Shirt fare trade" from "Property"
          And I check "Value"
         When I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page

    @javascript
    Scenario: Creating product with properties to choose
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And go to "Properties" tab
          And I click "Add property"
          And I select "Color" from "Property"
          And I select "red" from "Value"
         When I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page

    @javascript
    Scenario: Creating product with number property
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And go to "Properties" tab
          And I click "Add property"
          And I select "Color" from "Property"
          And I fill in "Value" with "12"
         When I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page

    Scenario: Created products appear in the list
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And I press "Create"
         When I go to the product index page
         Then I should see 5 products in the list
          And I should see product with name "Manchester United tee" in that list

    Scenario: Accessing the product editing form
        Given I am on the page of product "Super T-Shirt"
         When I follow "edit"
         Then I should be editing product "Super T-Shirt"

    Scenario: Accessing the editing form from the list
        Given I am on the product index page
         When I click "Edit" near "Mug"
         Then I should be editing product "Mug"

    Scenario: Updating the product name
        Given I am editing product "Sticker"
         When I fill in "Name" with "Big Sticker"
          And I press "Save changes"
         Then I should be on the page of product "Big Sticker"
          And I should see "Product has been successfully updated."

    Scenario: Updating the product description
        Given I am editing product "Sticker"
         When I fill in "Description" with "This sticker is awesome"
          And I press "Save changes"
         Then I should be on the page of product "Sticker"
          And I should see "This sticker is awesome"

    Scenario: Selecting the product tax category
        Given I am editing product "Sticker"
         When I select "Print" from "Tax category"
          And I press "Save changes"
         Then I should be on the page of product "Sticker"
          And I should see "Product has been successfully updated."
          And "Print" should appear on the page

    Scenario: Selecting the categorization taxons
        Given I am editing product "Black T-Shirt"
          And go to "Categorization" tab
         When I select "Premium T-Shirts" from "Category"
          And I select "Featured" from "Special"
          And I press "Save changes"
         Then I should be on the page of product "Black T-Shirt"
          And I should see "Product has been successfully updated."
          And "Featured" should appear on the page

    Scenario: Selecting more than one taxon from taxonomy
        Given I am editing product "Black T-Shirt"
          And go to "Categorization" tab
         When I select "Featured" from "Special"
          And I additionally select "New" from "Special"
          And I press "Save changes"
         Then I should be on the page of product "Black T-Shirt"
          And I should see "Product has been successfully updated."
          And "Featured" should appear on the page

    Scenario: Deleting product
        Given I am on the page of product "Mug"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the product index page
          And I should see "Product has been successfully deleted."

    @javascript
    Scenario: Deleting product with js modal
        Given I am on the page of product "Mug"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the product index page
          And I should see "Product has been successfully deleted."

    Scenario: Deleted product disappears from the list
        Given I am on the page of product "Sticker"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the product index page
          And I should not see product with name "Sticker" in that list

    @javascript
    Scenario: Deleted product disappears from the list with js modal
        Given I am on the page of product "Sticker"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the product index page
          And I should not see product with name "Sticker" in that list

    Scenario: Accessing the product details page from list
        Given I am on the product index page
         When I click "details" near "Mug"
         Then I should be on the page of product "Mug"
