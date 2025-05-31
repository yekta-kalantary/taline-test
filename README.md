# Taline Test

This is a Laravel-based test project simulating an order and wallet system with support for different asset types such as Rial and Gold.

---

## 🔧 Setup Instructions

Follow these steps to get the project up and running locally:

### 1. Clone the repository

```bash
git clone https://github.com/yekta-kalantary/taline-test.git
cd taline-test
````

### 2. Install dependencies

```bash
composer install
```

### 3. Copy `.env` file

```bash
cp .env.example .env
```

### 4. Set up your `.env` file

* Configure your **MySQL** database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

* Set cache store (for local testing, use `array`; for production or Redis-based queues, use `redis`):

```env
CACHE_STORE=array
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Run migrations and seeders

```bash
php artisan migrate --seed
```

This will seed the database with **temporary test users** and wallets.

---

## 👤 Test Users

| Name  | Email                                   | Password | Rial Balance | Gold Balance |
| ----- | --------------------------------------- | -------- | ------------ | ------------ |
| Ahmad | [ahmad@test.com](mailto:ahmad@test.com) | password | 800,000,000  | 7 grams      |
| Reza  | [reza@test.com](mailto:reza@test.com)   | password | 740,000,000  | 18 grams     |
| Akbar | [akbar@test.com](mailto:akbar@test.com) | password | 330,000,000  | 50 grams     |

---

## 🛠 Start Queue Worker

To process order matching and background jobs, run:

```bash
php artisan queue:work --queue=high,default
```
---

## 🛠 Post man collection

```bash
https://github.com/yekta-kalantary/taline-test/blob/main/taline.postman_collection.json
```
## 🛠 Api Doc
the api doc is on postman when importing post man collection in the side panel of each request you can see the doc

![Api Doc https://raw.githubusercontent.com/yekta-kalantary/taline-test/refs/heads/main/Screenshot%201404-03-10%20at%2012.56.28.png](https://raw.githubusercontent.com/yekta-kalantary/taline-test/refs/heads/main/Screenshot%201404-03-10%20at%2012.56.28.png)

