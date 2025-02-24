ASTUDIO Laravel EAV API - README

Overview
This is a RESTful API built with Laravel using the EAV (Entity-Attribute-Value) pattern. It supports:
- Dynamic attributes for entities (currently Projects, future-ready for any thing like,Customers,Tasks)
- API authentication with Laravel Passport
- CRUD operations for Users, Projects, Attributes, and AttributeValues
- Project filtering on regular and EAV attributes
- Database transactions for data consistency
- Postman collection for API testing

Getting Started

Installation
1. Clone the repository:
   bash
   git clone https://github.com/khaleelmew/project-management.git
   cd project-assessment
   
2. Install dependencies:
   bash
   composer install
   
3. Set up the environment:
   bash
   cp .env.example .env
   php artisan key:generate
   
4. Configure database in `.env`:
   env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
5. Run migrations and seeders:
   bash
   php artisan migrate --seed
   
6. Install Passport:
   bash
   php artisan passport:client --personal
   

Authentication
Handled with Laravel Passport.
- Register: POST `/api/register`
- Login: POST `/api/login`
- Logout: POST `/api/logout` (requires Bearer token)

Using Postman
1. Import `postman_collection.json` into Postman.
2. Use `/api/login` to get the access token.
3. Set `ACCESS_TOKEN` in collection variables for authenticated requests.

Models & Relationships

User
- Fields: first_name, last_name, email, password
- Relation: Belongs to many Projects

Project
- Fields: name, status
- Relations:
  - Belongs to many Users
  - Has many AttributeValues (polymorphic)
- On delete: Related attribute values and user links are deleted

Attribute
- Fields: name, type (text, date, number, select), slug, required, unique
- *** Used Liberty to add unique and required flags for attribute not completed but this very useful in Entity-Attribute-Value ***S

AttributeValue
- Links attributes to entities (Projects, etc.)
- Fields: attribute_id, entity_id, entity_type, value
- Uses polymorphic relation to support multiple entity types
- Prevents duplicates with `updateOrCreate`,

Project Filtering
Filter projects by fields or dynamic attributes using query parameters.

Operators Supported
=, >, <, >=, <=, LIKE, !=,  *** added more operators ***. 

Examples
- `/api/projects?filters[name]=ProjectX`
- `/api/projects?filters[department]=IT`
- `/api/projects?filters[start_date][>=]=2025-02-01`
- `/api/projects?filters[name][LIKE]=%Alpha%`

API Endpoints
- Users: `/api/users`
- Projects: `/api/projects`
- Attributes: `/api/attributes`
- Attribute Values: `/api/attribute-values`

Refer to the Postman collection for request and response details.

Notes
- API examples are available in the Postman collection.
- Filtering works for both regular and dynamic attributes.
- No additional setup required beyond the steps listed.
