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
│ │ ├── Controllers/
│ │ │ └── CompanyPutController.php
│ │ ├── Requests/
│ │ │ └── CompanyPutRequest.php
│ ├── User/
│ │ ├── Controllers/
│ │ │ └── UserPutController.php
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
<?php declare(strict_types=1);

namespace App\Modules\Company\Controllers;

use App\Models\Company;
use App\Modules\Company\Requests\CompanyPutRequest;
use App\Modules\Company\Requests\CompanyPutRequestDTO;
use App\Presenters\JsonPresenter;
use App\Services\StorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyPutController
{
    public function __construct(
        private CompanyPutUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(CompanyPutRequest $request)
    {
        $DTO = $request->getValidated();
        $response = $this->useCase->execute($DTO);
        return $this->presenter->present($response);
    }
}

class CompanyPutUseCase
{
    public function __construct(
        private CompanyPutRepository $repository
    )
    {
    }

    public function execute(CompanyPutRequestDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (\Throwable $exception) {
            throw new \Exception('Error during transaction', 0, $exception);
        }
    }
}

class CompanyPutRepository
{
    public function __construct(
        private Company $company,
        private StorageService $service
    )
    {
    }

    public function make(CompanyPutRequestDTO $DTO): array
    {
        $this->company = Company::find(Auth::user()->company_id);
        $this->attachImage($DTO);
        $this->company->update($DTO->toArray(['logo']));
        return [
            'message' => 'Successfully updated!'
        ];
    }

    private function attachImage(CompanyPutRequestDTO $DTO)
    {
        if (isset($DTO->logo)) {
            $url = $this->service->putOne($DTO->logo, 'logos/');
            $this->company->avatar_url = $url;
            $this->company->save();
        }
    }
}
```
### Request
```php
<?php declare(strict_types=1);

namespace App\Modules\Company\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class CompanyPutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:30',
            'logo' => 'file|mimes:jpeg,jpg,png,webp',
            'description' => 'string|max:60',
            'website_url' => 'string|url|max:120',
        ];
    }

    public function getValidated(): CompanyPutRequestDTO
    {
        return new CompanyPutRequestDTO(...$this->validated());
    }
}

readonly class CompanyPutRequestDTO extend DataTransferObject
{
    public function __construct(
        public ?string $name = null,
        public ?UploadedFile $logo = null,
        public ?string $description = null,
        public ?string $website_url = null,
    ) {}
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
2. Create your modules in the `app/Modules` directory. Each module should have `Controllers` and `Requests` folders.
3. Define your controllers, requests, and DTOs within their respective modules.

## Contributing
I'm welcome contributions! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes with clear messages.
4. Push to your branch.
5. Open a pull request.

## License
This project is licensed under the MIT License. See the LICENSE file for more information.
