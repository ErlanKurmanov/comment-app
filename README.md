# Comments API

A RESTful API implementation for a content commenting system. This project supports news and video entities, nested comments (replies), and polymorphic associations. It features cursor-based pagination for high performance.


## Installation and Setup

Follow these steps to deploy the application locally using Laravel Sail.

**1. Clone the repository**

```bash
git clone https://github.com/ErlanKurmanov/comment-app.git
cd comment-app

```

**2. Configure Environment**

Create the environment file from the example.

```bash
cp .env.example .env

```

**3. Install Dependencies**

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
    
./vendor/bin/sail up -d
./vendor/bin/sail composer install

```


**4. Application Key & Migrations**

Generate the application key and run database migrations with seeds.

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

```

The seeder will create:

* A test user (`test@example.com`)
* A sample News post ("Laravel 12 Release News")
* A tree of nested comments for testing pagination.

## Authentication

This API uses Laravel Sanctum for authentication. To test protected endpoints (creating comments), you need a Bearer Token.

**Option A: Generate Token via Console (Recommended)**

Since this is a backend-only test task, the quickest way to get a token for the seeded user is via Tinker:

```bash
./vendor/bin/sail artisan tinker

```

Inside the Tinker console, run:

```php
$user = App\Models\User::where('email', 'test@example.com')->first();
echo $user->createToken('review-token')->plainTextToken;

```

Copy the output string. You will use this as your `Authorization: Bearer <token>` header.

## API Endpoints

Base URL: `http://localhost/api`

### 1. Public Endpoints

**Get News List**

* **Method:** `GET`
* **URL:** `/api/news`

**Get Single News with Comments**

* **Method:** `GET`
* **URL:** `/api/news/1`
* **Description:** Returns the news post and top-level comments. Includes `meta` data for cursor pagination (`next_cursor`, `prev_cursor`).

**Get Video Posts**

* **Method:** `GET`
* **URL:** `/api/videos`

### 2. Protected Endpoints

*Requires Header:* `Authorization: Bearer <your-token>`

**Create a Comment (Root)**

* **Method:** `POST`
* **URL:** `/api/comments`
* **Body (JSON):**
```json
{
    "body": "This is a root comment on a news post",
    "commentable_type": "news",
    "commentable_id": 1
}

```



**Create a Reply (Nested)**

* **Method:** `POST`
* **URL:** `/api/comments`
* **Body (JSON):**
```json
{
    "body": "This is a reply to comment #5",
    "commentable_type": "news",
    "commentable_id": 1,
    "parent_id": 5
}

```


*Note: `commentable_id` must always refer to the root entity (News or Video), not the parent comment.*

**Delete Comment**

* **Method:** `DELETE`
* **URL:** `/api/comments/{id}`


## Architecture Notes

* **Polymorphism:** The `comments` table uses a polymorphic relation (`commentable_type`, `commentable_id`) mapped via `Relation::enforceMorphMap` in the Service Provider.
* **Recursion:** Comments are stored using an Adjacency List pattern (`parent_id`). The API returns a nested structure using recursive API Resources.
* **Performance:** The "Get News" endpoint uses `cursorPaginate` to handle large datasets efficiently, preventing offset-based performance issues.
