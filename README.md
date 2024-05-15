# Compact Layer Modules Architecture

Welcome to the Compact Layer Modules Architecture repository! This architecture is designed to be efficient, modular, and scalable, making it ideal for large projects. It aims to balance simplicity and functionality, ensuring that your code remains clean and maintainable even as your project grows.

## Table of Contents

1. [Introduction](#introduction)
2. [Key Features](#key-features)
3. [Folder Structure](#folder-structure)
4. [Example Code](#example-code)
   - [Controller](#controller)
   - [Request](#request)
5. [Installation](#installation)
6. [Usage](#usage)
7. [Contributing](#contributing)
8. [License](#license)

## Introduction

The Compact Layer Modules (CLM) architecture offers a structured approach to building large-scale applications. Each module contains its own controllers, requests, and related logic, allowing for clear separation of concerns and easier management of dependencies.

## Key Features

- **Modular Structure**: Each module is self-contained, promoting isolation and independence.
- **Scalability**: Easily scale your application by adding new modules without affecting existing functionality.
- **Maintainability**: Clean and organized codebase, making it easier to manage and understand.
- **Flexibility**: Supports modern PHP features and best practices.

## Folder Structure

Here's an example of the folder structure for a project using the CLM architecture:
```
app/
├── Modules/
│ ├── Company/
│ │ ├── Managers/
│ │ │ └── CompanyPutManager.php
│ │ ├── Requests/
│ │ │ └── CompanyPutRequest.php
│ ├── User/
│ │ ├── Managers/
│ │ │ └── UserPutManager.php
│ │ ├── Requests/
│ │ │ └── UserPutRequest.php
├── Models/
│ ├── Company.php
│ ├── User.php
config/
routes/
└── api.php
```

## Example Code

### Controller

```php
<?php

use App\Helpers\Presenters\JsonPresenter;
use Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Requests\UserAuthDTO;
use Requests\UserAuthRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserAuthController
{
    public function __construct(
        private UserAuthUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(UserAuthRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class UserAuthUseCase
{
    public function __construct(
        private UserAuthRepository $repository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function execute(UserAuthDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (InvalidCredentialsException $exception) {
            throw new Exception('Invalid credentials', Response::HTTP_FORBIDDEN, $exception);
        } catch (Throwable $exception) {
            throw new Exception('Service temporary unavailable', Response::HTTP_OK, $exception);
        }
    }
}

class UserAuthRepository
{
    public function __construct()
    {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function make(UserAuthDTO $DTO): array
    {
        if(Auth::attempt([$DTO->toArray()])){
            return [
                'success' => true,
                'message' => 'User successfully logged in',
                'token' => Auth::user()->createToken('auth-token')->plainTextToken,
            ];
        } else {
            throw new InvalidCredentialsException();
        }
    }
}
```
### Request
```php
<?php declare(strict_types=1);

namespace Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class UserAuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
            'example_parameter' => 'nullable|string'
        ];
    }

    public function getValidated(): UserAuthDTO
    {
        return new UserAuthDTO(
            ...$this->validated()
        );
    }
}

readonly class UserAuthDTO extends DataTransferObject
{
    public function __construct(
        public string $email,
        public ?string $example_parameter = null
        public string $password,
    )
    {
    }
}
```
## Installation

To install this architecture, follow these steps:

WIP

## Usage
To use this architecture, follow these steps:

1. Define your routes in routes/api.php:
```php
use App\Modules\Company\Controllers\CompanyPutController;

Route::put('company', CompanyPutController::class);
```
2. Create your modules in the `app/Modules` directory. Each module should have `Managers` and `Requests` folders.
3. Define your controllers, usecases, repositories, requests, and DTOs within their respective modules.

## Contributing
I'm welcome to contributions! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes with clear messages.
4. Push to your branch.
5. Open a pull request.

## License
This project is licensed under the MIT License. See the LICENSE file for more information.
