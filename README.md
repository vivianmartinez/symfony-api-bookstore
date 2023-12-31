# Symfony API Rest Bookstore  FOSRestBundle

Project generated with [Symfony](https://symfony.com/doc/current/setup.html#creating-symfony-applications) version 6.3.7 to create API with [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle). Optionally, you can also install [Symfony CLI](https://symfony.com/download). 

This API let you manage a Bookstore: Get information about books, authors, categories, assign tags. The user must be authorized to make the API call with an Authentication Token.

## Install

```
  $ symfony new my_project
  $ composer require symfony/orm-pack
  $ composer require --dev symfony/maker-bundle
  $ composer require symfony/serializer
  $ composer require friendsofsymfony/rest-bundle
  $ composer require symfony/validator
  $ composer require form
```
### Custom Authentication
Install symfony security-bundle
```
  $ composer require symfony/security-bundle
```
Then to create a custom authenticator you can follow the instructions here: [How to write a Custom Authenticator](https://symfony.com/doc/current/security/custom_authenticator.html) <br>
or <br>
Generate a form login [How to build a Login Form](https://symfony.com/doc/5.2/security/form_login_setup.html) and choose Empty Authenticator
```
  $ php bin/console make:auth

  What style of authentication do you want? [Empty authenticator]:
   [0] Empty authenticator
   [1] Login form authenticator
   >
  The class name of the authenticator to create (e.g. AppCustomAuthenticator):
   > ApiTokenAuthenticator
  
   created: src/Security/ApiTokenAuthenticator.php
   updated: config/packages/security.yaml
```
Then you have to configure your ApiTokenAuthenticator.

### JSON Login
To create Api Login Controller [Symfony JSON Login](https://symfony.com/doc/current/security.html#json-login)
```
  $ bin/console make:controller --no-template ApiLogin

  created: src/Controller/ApiLoginController.php
```
### Integrate Flysystem library to upload files
[Flysystem-blundle](https://github.com/thephpleague/flysystem-bundle)
```
  $ composer require league/flysystem-bundle
```
## API Documentation

#### POST: Register user

To register user, send json with username and password
```
  http://127.0.0.1:8000/api/user/register
```
<img src="/readme-pictures/post-user.png" width="800">

#### POST: Login

When user log receive an Authentication Token for secure access. The token will expire in one hour.
```
  http://127.0.0.1:8000/api/login
```
<img src="/readme-pictures/post-login.png" width="800">

#### After Login use the Token to make requests. Here you have some of Token errors validations:

Send the authentication Token **X-AUTH-TOKEN** in the **Headers**.

##### Expired token

<img src="/readme-pictures/expired-token.png" width="800">

##### Not Api Token provided

<img src="/readme-pictures/noapitoken-provided.png" width="800">

##### Invalid Credentials if the token is wrong

<img src="/readme-pictures/invalid-credentials.png" width="800">

#### GET: Books
To get list of books you must send header **X-AUTH-TOKEN** that you received after login.
```
  http://127.0.0.1:8000/api/books
```
<img src="/readme-pictures/get-books.png" width="800">

#### GET: Single Book
To get single book specify the book id on the request and authenticate. In this case, the book id is specified with 7.
```
  http://127.0.0.1:8000/api/book/7
```
<img src="/readme-pictures/get-book-single.png" width="800">

#### POST: Create Book

To create a Book send json with data, title, price, author and category cannot be null. <br>
To upload Image you must send the file coverted on base 64. <br>
You can add a tag or create one, To add a tag send the tag id, to create a tag at the same time send a new tag name.

```
  http://127.0.0.1:8000/api/book/create

  {
      "title": "Name book",
      "description": "description book",
      "price": 00.00,
      "author": 1,
      "category": 1,
      "tags":{
          "0":{
              "id": 1
          },
          "1":{
              "name": "Name new tag"
          }
      }
  }
```
<img src="/readme-pictures/post-book-image.png" width="800">

#### PATCH: Update Book
To update a book specify the book id on the request and authenticate. You must send a json with modifications, you can add tags to the book or create the tags directly.
```
  http://127.0.0.1:8000/api/book/update/1
```
Update book and add Tag - to add a tag send the id:

<img src="/readme-pictures/patch-book.png" width="800">

Update book and create tag - to create a new tag send the name:

<img src="/readme-pictures/patch-book-create-tag.png" width="800">

#### PATCH: Update Book - delete tag from Book
If you want to delete a tag from a book you can make this request. You must send a json with the tags ids you want to delete.
```
  http://127.0.0.1:8000/api/book/12/delete/tags
```
<img src="/readme-pictures/delete-tag-book.png" width="800">

#### DELETE: book
To delete a book specify the book id on the request and authenticate. 
```
  http://127.0.0.1:8000/api/book/delete/12
```
#### GET: Authors
```
  http://127.0.0.1:8000/api/authors

  //GET single author
  http://127.0.0.1:8000/api/author/2
```
#### POST: Author
```
  http://127.0.0.1:8000/api/author/create
```
<img src="/readme-pictures/post-author.png" width="800">

#### PATCH: Update Author
To update Author send json with name modification and specify author id.
```
  http://127.0.0.1:8000/api/author/update/10
```
#### DELETE: Delete Author
To delete author specify author id. 
```
  http://127.0.0.1:8000/api/author/delete/17
```
#### GET: Categories
```
  http://127.0.0.1:8000/api/categories

  //GET single category
  http://127.0.0.1:8000/api/category/2
```
#### POST: Category
```
  http://127.0.0.1:8000/api/category/create
```
<img src="/readme-pictures/post-category.png" width="800">

#### PATCH: Update Category
To update Category send json with name modification and specify category id.
```
  http://127.0.0.1:8000/api/category/update/3
```
#### DELETE: Delete Category
To delete category specify category id. 
```
  http://127.0.0.1:8000/api/category/delete/10
```
#### GET: Tags
```
  http://127.0.0.1:8000/api/tags

  //GET single author
  http://127.0.0.1:8000/api/tag/2
```
#### POST: Tag
To create tag send json with tag name.
```
  http://127.0.0.1:8000/api/tag/create
  {
    "name": "name tag"
  }
```
#### PATCH: Update Tag
To update a tag send json with tag name modification.
```
  http://127.0.0.1:8000/api/tag/1
```
#### DELETE: Delete Tag
To delete tag specify tag id. 
```
  http://127.0.0.1:8000/api/tag/delete/12
```

## Development server

Run symfony server:start for a dev server. Navigate to `http://127.0.0.1:8000/`.
