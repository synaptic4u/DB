    # DB Package

    The DB package is a database wrapper class for PDO, enabling custom scripting and simplifying database interactions within your PHP applications.

    ## Overview

    This package provides an interface and implementation for database operations using PDO. It includes classes for handling database connections and executing queries with ease, allowing for more maintainable and secure database interactions.

    ## Installation

    To install the package to your existing application, use Composer:

    ```bash
    composer require synaptic4u/db
    ```

    ## Test

    To test the package, run in terminal (Linux CLI) of the project's root directory:

    ```bash 
    php tests/DBTest.php
    ```

    ## Usage

    Here's a basic example of how to use the DB package:

    ```php
    use Synaptic4U\DB\DB;
    use Synaptic4U\DB\MYSQL;

    // Initialize the database connection
    $db = new DB(new MYSQL());

    // Execute a query with parameters
    $result = $db->query(['param1' => 'value1'], 'SELECT * FROM table WHERE column = :param1');

    // Process the results
    foreach ($result as $row) {
        // Handle each row
    }
    ```

    ## Features

    - PDO-based database abstraction
    - Prepared statements for secure queries
    - Support for multiple database types
    - Simple and intuitive API
    - Error handling and logging

    ## Contributing

    Contributions are welcome! Please follow these steps:

    1. Fork the repository
    2. Create a new branch for your feature or bugfix
    3. Write tests for your changes
    4. Ensure all tests pass
    5. Submit a pull request

    ## License

    This package is open-source and available under the MIT License.
