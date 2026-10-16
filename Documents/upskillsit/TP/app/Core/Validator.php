<?php

namespace App\Core;

final class Validator
{
    public array $errors = [];

    public function required(array $data, array $fields): self
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                $this->errors[$field] = 'Ce champ est obligatoire.';
            }
        }
        return $this;
    }

    public function email(array $data, string $field): self
    {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Adresse email invalide.';
        }
        return $this;
    }

    public function min(array $data, string $field, int $length): self
    {
        if (!empty($data[$field]) && mb_strlen((string) $data[$field]) < $length) {
            $this->errors[$field] = "Minimum {$length} caractères.";
        }
        return $this;
    }

    public function same(array $data, string $field, string $other): self
    {
        if (($data[$field] ?? null) !== ($data[$other] ?? null)) {
            $this->errors[$field] = 'Les valeurs ne correspondent pas.';
        }
        return $this;
    }

    public function ok(): bool
    {
        return $this->errors === [];
    }
}
