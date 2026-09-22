# E-Commerce API Documentation

## Base URL

- Local: http://localhost:8000/api/v1

## Authentication

This API uses Laravel Sanctum token authentication.

- Register or login to receive a bearer token.
- Include the token in the Authorization header:

  Authorization: Bearer <token>

## Authentication Endpoints

### Register

- Method: POST
- URL: /api/v1/register
- Body:

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Example response:

```json
{
  "message": "User registered successfully.",
  "token": "<sanctum-token>",
  "data": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "is_admin": false
  }
}
```

### Login

- Method: POST
- URL: /api/v1/login
- Body:

```json
{
  "email": "jane@example.com",
  "password": "password123"
}
```

### Logout

- Method: POST
- URL: /api/v1/logout
- Requires: Bearer token

### Get Authenticated User

- Method: GET
- URL: /api/v1/user
- Requires: Bearer token

### Update Profile

- Method: PUT
- URL: /api/v1/user/profile
- Requires: Bearer token
- Body:

```json
{
  "name": "Jane Updated",
  "email": "jane.updated@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

## Public Catalog Endpoints

### Categories

- Method: GET
- URL: /api/v1/categories

Example response:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "description": "Smart devices and gadgets for every day use.",
      "image": null
    }
  ]
}
```

### Category Details

- Method: GET
- URL: /api/v1/categories/{slug}

### Products

- Method: GET
- URL: /api/v1/products
- Query params:
  - search
  - category
  - min_price
  - max_price
  - sort (price_asc, price_desc, newest)
  - page

Example request:

```http
GET /api/v1/products?search=headphones&min_price=50&max_price=200&sort=price_asc
```

### Featured Products

- Method: GET
- URL: /api/v1/products/featured

### Product Details

- Method: GET
- URL: /api/v1/products/{slug}

## Cart Endpoints

- Method: GET
- URL: /api/v1/cart
- Requires: Bearer token

- Method: POST
- URL: /api/v1/cart
- Requires: Bearer token
- Body:

```json
{
  "product_id": 2,
  "quantity": 1
}
```

- Method: PATCH
- URL: /api/v1/cart/{productId}
- Body:

```json
{
  "quantity": 3
}
```

- Method: DELETE
- URL: /api/v1/cart/{productId}
- Method: DELETE
- URL: /api/v1/cart

## Orders and Checkout

### Checkout

- Method: POST
- URL: /api/v1/orders/checkout
- Requires: Bearer token

Example body:

```json
{
  "shipping_name": "Jane Doe",
  "shipping_email": "jane@example.com",
  "shipping_phone": "123456789",
  "shipping_address": "123 Main Street",
  "shipping_city": "Boston",
  "shipping_state": "MA",
  "shipping_postal_code": "02108",
  "shipping_country": "US",
  "payment_method": "card",
  "coupon_code": "SAVE10",
  "notes": "Handle with care"
}
```

### List Orders

- Method: GET
- URL: /api/v1/orders
- Requires: Bearer token

### Order Details

- Method: GET
- URL: /api/v1/orders/{orderId}
- Requires: Bearer token

### Cancel Order

- Method: POST
- URL: /api/v1/orders/{orderId}/cancel
- Requires: Bearer token

## Coupon Validation

- Method: POST
- URL: /api/v1/coupons/validate
- Body:

```json
{
  "code": "SAVE10",
  "subtotal": 120
}
```

Example response:

```json
{
  "message": "Coupon applied successfully.",
  "data": {
    "code": "SAVE10",
    "discount": 12,
    "subtotal": 120,
    "total": 108
  }
}
```

## Error Responses

Common API errors:

- 401 Unauthorized: missing or invalid token
- 403 Forbidden: user is not authorized to view another user's order
- 404 Not Found: item or order does not exist
- 422 Unprocessable Entity: invalid payload, invalid stock, coupon validation failed
- 500 Server Error: unexpected backend failure

Example error:

```json
{
  "message": "Only 3 item(s) of Product Name are available."
}
```
