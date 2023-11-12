# Authficate API

An API built using Laravel to manage certificates.

## Table of Contents

-   [Introduction](#introduction)
-   [Prerequisites](#prerequisites)
-   [Installation](#installation)
-   [API Reference](#api-reference)
    -   [Register](#register)
    -   [Login](#login)
    -   [Logout](#logout)
    -   [Get a user](#get-a-user)
    -   [Get user certificates](#get-user-certificates)
    -   [Search for a certificate by id](#search-for-a-certificate-by-id)

## Introduction

Certificate Authentication API, a robust and versatile API developed using Laravel. This API is your solution for managing certificate authentication with ease.

## Prerequisites

-   PHP 8.2
-   Composer
-   Database (MySQL)

## Installation

**Note**: Before you start, make sure you have all the prerequisites in place as mentioned in the previous step.

1. Clone the Project:

```bash
git clone https://github.com/MohAlkurdi/Authficate-API.git
```

2. Configure Environment Variables:

```bash
cp .env.example .env
```

3. Install Composer Dependencies:

```bash
composer install
```

4. Generate Application Key:

```bash
php artisan key:generate
```

5. Run Database Migrations:

```bash
php artisan migrate
```

6. Start the Development Server:

```bash
php artisan serve
```

-   Laravel application should now be accessible at http://localhost:8000

## API Reference

#### Register

```http
POST /api/register
```

-   **Request Body**

```json
{
    "name": "Moh",
    "email": "m@m.com",
    "password": "12345678",
    "password_confirmation": "12345678"
}
```

#### Login

```http
POST /api/login
```

-   **Request Body**

```json
{
    "email": " m@m.com",
    "password": "12345678"
}
```

#### Logout

```http
POST /api/logout
```

**Authentication:**

Include a Bearer Token in the `Authorization` header of your HTTP request.

| Header          | Value                      |
| :-------------- | :------------------------- |
| `Authorization` | `Bearer <your_token_here>` |

#### Get a user

```http
GET /api/user
```

| Header          | Value                      |
| :-------------- | :------------------------- |
| `Authorization` | `Bearer <your_token_here>` |

---

#### Get user certificates

```http
POST /api/get-certificates
```

-   **Request Body**

```json
{
    "email": " m@m.com",
    "password": "12345678"
}
```

| Header          | Value                      |
| :-------------- | :------------------------- |
| `Authorization` | `Bearer <your_token_here>` |

#### Search for a certificate by id

```http
GET /api/get-certificate/{id}
```

| Parameter | Type     | Description                  |
| :-------- | :------- | :--------------------------- |
| `id`      | `string` | **Required**. Certificate Id |
