Feature: Products
    As a store owner
    I want to be able to manage products
    In order to create my offer

    Background:
        Given I am logged in as administrator
          And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
          And there are following properties:
            | name               | presentation |
            | T-Shirt fabric     | T-Shirt      |
          And the following products exist:
            | name          | price | options                     | properties             |
            | Super T-Shirt | 19.99 | T-Shirt size, T-Shirt color | T-Shirt fabric: Wool   |
            | Black T-Shirt | 19.99 | T-Shirt size                | T-Shirt fabric: Cotton |
            | Mug           | 5.99  |                             |                        |
            | Sticker       | 10.00 |                             |                        |
          And product "Super T-Shirt" is available in all variations

    Scenario: Seeing index of all products
        Given I am on the dashboard page
         When I follow "Products"
         Then I should be on the product index page
          And I should see 4 products in the list

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
         When I press "Create"
         Then I should still be on the product creation page
          And I should see "Please enter product description."

    Scenario: Creating simple product without any properties and options
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Book about Everything   |
            | Description | Interesting description |
            | Price       | 29.99                   |
         When I press "Create"
         Then I should be on the page of product "Book about Everything"
          And I should see "Product has been successfully created."

    Scenario: Creating product with options
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And I select "T-Shirt size" from "Options"
         When I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page
          And I should see "T-Shirt size"

    @javascript
    Scenario: Creating product with properties
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Manchester United tee   |
            | Description | Interesting description |
            | Price       | 59.99                   |
          And go to "Properties" tab
          And I click "Add property"
          And I select "T-Shirt fabric" from "Property"
          And I fill in "Value" with "Cotton"
         When I press "Create"
         Then I should be on the page of product "Manchester United tee"
          And I "Product has been successfully created." should appear on the page
          And I should see "Cotton"

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
         When I follow "Edit"
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

    Scenario: Deleting product
        Given I am on the page of product "Mug"
         When I follow "Delete"
         Then I should be on the product index page
          And I should see "Product has been successfully deleted."

    Scenario: Deleted product disappears from the list
        Given I am on the page of product "Sticker"
         When I follow "Delete"
         Then I should be on the product index page
          And I should not see product with name "Sticker" in that list

    Scenario: Accessing the product details page from list
        Given I am on the product index page
         When I click "Details" near "Mug"
         Then I should be on the page of product "Mug"
