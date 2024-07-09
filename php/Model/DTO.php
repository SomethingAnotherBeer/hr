<?php
namespace Model;
use Exception\ValidationException;
use System\Http\Data\Request;
use System\Structure\HashCollectionImmutable;

abstract class DTO
{

    public static function getInstance(Request $request): static
    {
        return new static($request);
    }


    public function checkNotNullOrEmptyByRequired(Request|HashCollectionImmutable $data, array $expected_params): void
    {
        foreach ($expected_params as $expected_param) {
            if (!$data->$expected_param) {
                throw new ValidationException("{$expected_param} is null or empty", 400);
            }
        }
    }

    public function checkNumericByRequired(Request|HashCollectionImmutable $data, array $expected_params): void
    {
        foreach ($expected_params as $expected_param) {
            if (!is_numeric($data->$expected_param)) {
                throw new ValidationException("{$expected_param} is not numeric", 400);
            }
        }
    }

    public function checkIsStringByRequired(Request|HashCollectionImmutable $data, array $expected_params): void
    {
        foreach ($expected_params as $expected_param) {
            if (!is_string($data->expected_param)) {
                throw new ValidationException("{$expected_param} is not string", 400);
            }
        }
    }


}