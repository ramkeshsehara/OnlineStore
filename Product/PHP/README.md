# Project Title

Design a RESTful API for an online store which can be used to manage different products

### Prerequisites

1. clone it from here https://github.com/ramkeshsehara/OnlineStore.git

### Installing

1. create a database

## Run

1. run setup.php enter username, password, database name ,Host url for database connection.  

# Run test Case 

1. Product name should not contain white space.

2. Test cases for add , edit , delete product are inter related so make sure before update and delete a product you have added that product in database in add product test case.

3. During update product name make sure you are updating with name which don't exist in database because product name is unique if already exist it will not update in database.

4. Make sure during delete test case you are deleting all product which you have added either when add test case run again product name already exist in the database so test case will fail. 

