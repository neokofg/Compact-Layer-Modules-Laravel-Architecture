<?php declare(strict_types=1);

namespace App\Services;

use Dadata\DadataClient;

class DadataService
{
    private DadataClient $Dadata;

    public function __construct()
    {
        $this->Dadata = new DadataClient(env('DADATA_API_KEY'), env('DADATA_SECRET_KEY'));
    }

    public function findById(string $id):array
    {
        return $this->Dadata->findById("address", $id, 1);
    }

    public function clean(string $address): array
    {
        return $this->Dadata->clean("address", $address);
    }
}
