# Order & Payment Management API
This is a Laravel-based API designed for the management of orders and payments. I've built this with a focus on **clean code**, **solid architecture**, and **extensibility**.

## Getting Started

To get this project running on your machine, follow these simple steps:

1.  **Install Dependencies**: Run `composer install`.
2.  **Environment Setup**: Copy `.env.example` to `.env`.
3.  **Database**: The project uses SQLite by default. Run `php artisan migrate`.
4.  **JWT Secret**: Generate the security token with `php artisan jwt:secret`.
5.  **Run Service**: Start the server using `php artisan serve`.


## Design & Architecture
The project follows modern design patterns to ensure scalability and maintainability.

### Repository Design Pattern
We utilize the **Repository Pattern** to decouple the business logic from the data access layer (Eloquent). 
- **Base Repository**: A generic `EloquentBaseRepository` handles common CRUD operations.
- **Interfaces**: All repositories implement specific interfaces (e.g., `UserRepositoryInterface`), allowing for easy mocking during testing or swapping out the data source.
- **Service Provider**: `RepositoryServiceProvider` binds the interfaces to their concrete implementations.


###  The Strategy Pattern (Payment Gateways)
The payment system is highly extensible thanks to the **Strategy Pattern**.
- **Interface**: `App\Services\Payments\PaymentGatewayInterface` defines the contract for all gateways.
- **Factory**: `App\Services\Payments\PaymentGatewayFactory` instantiates the correct gateway dynamically.
- **Extensibility**: Adding a new gateway like Stripe is as simple as creating a new class and registering it in the factory.



###  Response Trait
To maintain a consistent API structure, we use a centralized `ApiResponseTrait`.
- **Location**: `App\Traits\ApiResponseTrait`
- **Utility**: Provides standard `successResponse()` and `errorResponse()` methods used across all controllers.
- **Consistency**: Ensures that every API response follows the same JSON schema: `{ status, message, data|errors }`.

## Authentication
We use **JWT (JSON Web Tokens)** for secure, stateless authentication. 
- **Register**: Managed via `UserRepository` with automatic password hashing.
- **Protected Routes**: Secure endpoints require the `Authorization: Bearer <token>` header.

## API Endpoints

### Authentication
- `POST /api/auth/register` - Create account.
- `POST /api/auth/login` - Get token.
- `GET /api/auth/me` - Get current user profile.

### Orders
- `GET /api/orders` - List orders (with optional `status` filter).
- `POST /api/orders` - Create a new order (handled via `OrderRepository` with transaction support).
- `GET /api/orders/{id}` - View order details.
- `PUT /api/orders/{id}` - Update order details.
- `DELETE /api/orders/{id}` - Delete order (prevented if payments exist).

### Payments
- `GET /api/payments` - List all payments.
- `POST /api/payments` - Process a payment for a **confirmed** order (handled via `PaymentRepository`).

## Testing
Includes feature tests to validate repository logic, gateway strategies, and controller responses.
Run them with:
```bash
php artisan test
```

Enjoy using the API!
