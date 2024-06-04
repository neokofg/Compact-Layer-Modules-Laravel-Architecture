<?php declare(strict_types=1);

namespace App\Helpers;

readonly class DataTransferObject
{
    public function toArray(array $except = []): array
    {
        $data = get_object_vars($this);
        foreach($data as $key => $value) {
            if (is_null($value) || in_array($key, $except)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
