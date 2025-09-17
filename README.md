# Product Management System - Backend

This is the **backend** of the Product Management System, built with **Laravel (PHP)**.  
It exposes a RESTful API to be consumed by the frontend (React).

---

## 🚀 Features
- Laravel 10+ (MVC structure).
- REST API for product CRUD.
- JWT-based authentication.
- Database migrations & seeders.
- API Resource formatting for consistent responses.
- Role-based access control (optional).

---

## 📦 Tech Stack
- **Laravel** (PHP framework)
- **MySQL** (database)
- **JWT Auth** (authentication)
- **Composer** (dependency manager)

---

## ⚙️ Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/23rohitmaji/product-management-system-backend.git
   cd product-management-system-backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the example environment file to create your own `.env` file:
   ```bash
   cp .env.example .env
   ```
   Update the following in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=product_management
   DB_USERNAME=root
   DB_PASSWORD=YOUR_SQL_PASSWORD
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```
   The API will run on:
   ```
   http://127.0.0.1:8000
   ```

---

## 📖 API Endpoints

| Method     | Endpoint                      | Description               |
|------------|-------------------------------|---------------------------|
| POST       | `/api/login`                  | User login                |
| POST       | `/api/register`               | User registration         |
| GET        | `/api/products`               | List all active products  |
| GET        | `/api/products/deleted`       | List all deleted products |
| POST       | `/api/products`               | Create new product        |
| PUT        | `/api/products/{id}`          | Update product            |
| DELETE     | `/api/products/{id}`          | Delete product            |
| POST       | `/api/products/{id}/restore`  | Create new product        |
| GET        | `/api/categories`             | List all categories       |
| POST       | `/api/categories`             | Add new category          |
| PUT        | `/api/categories/{id}`        | Update existing category  |
| GET        | `/api/cart`| Get all cart items                           |
| POST       | `/api/cart`| Add items in cart                            |
| PUT        | `/api/cart/{product_id}`| Update item quantity in cart    |
| DELETE     | `/api/cart/{product_id}`| Delete product from cart        |


---
