# Payment API
___

## Built with

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
<br>
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)
<br>
![Swagger](https://img.shields.io/badge/-Swagger-%23Clojure?style=for-the-badge&logo=swagger&logoColor=white)

## Usage

```bash

// start local server
composer start

//run tests
composer test

//update database and built tables
php bin/doctrine orm:schema-tool:update --force

```
## Requirements

1. **MariaDB**: Make sure you have MariaDB installed to run the database for this API.

## Setup

1. **Environment File**: Create an `.env` file in the project's root directory with the following configurations:

    ```bash
    DRIVER=
    HOST=
    DATABASE=
    USERNAME=
    PASSWORD=
    ENVIRONMENT=
    ```

2. **Database Seeding**: Use the `seed.sql` file provided to seed your MariaDB database.

## Usage

To use this API, follow these steps:

1. **Registration**: Begin by registering your account to obtain a JWT token. You can register at `/register`.

2. **Accessing API Documentation**: The API documentation (API Docs) is accessible at `/docs`.

3. **HTTP Headers**: Use the following HTTP header for request authentication:

    - **jwt_token**: Include your JWT token obtained during registration.

## API Endpoints

**Methods:**

- **GET /v1/methods**: Get a list of all payment methods.
- **POST /v1/methods**: Add a new payment method.
- **DELETE /v1/methods/{id}**: Delete a payment method by ID.
- **GET /v1/methods/deactivate/{id}**: Deactivate a payment method by ID.
- **GET /v1/methods/reactivate/{id}**: Reactivate a payment method by ID.
- **PUT /v1/methods/{id}**: Update a payment method by ID.

**Customers:**

- **GET /v1/customers**: Get a list of all customers.
- **POST /v1/customers**: Add a new customer.
- **DELETE /v1/customers/{id}**: Delete a customer by ID.
- **PUT /v1/customers/{id}**: Update a customer by ID.
- **GET /v1/customers/deactivate/{id}**: Deactivate a customer by ID.
- **GET /v1/customers/reactivate/{id}**: Reactivate a customer by ID.

**Payments (Transactions):**

- **GET /v1/payments**: Get a list of all payments (transactions).
- **POST /v1/payments**: Add a new payment (transaction).
- **DELETE /v1/payments/{id}**: Delete a payment (transaction) by ID.
- **PUT /v1/payments/{id}**: Update a payment (transaction) by ID.

Make sure you include the required JWT token in the HTTP header for authenticated requests. Refer to the API documentation for more details on using these endpoints.

Feel free to enhance this README with additional details specific to your project, such as the technologies used, dependencies, and any special considerations.
