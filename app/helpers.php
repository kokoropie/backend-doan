<?php 
if (!function_exists('values_of_enum')) {
    /**
     * Get the values of an enum.
     *
     * @param class-string $enum
     * @return list<string>
     */
    function values_of_enum(string $enum): array
    {
        return array_map(fn ($e) => $e->value, $enum::cases());
    }
}

if (!function_exists('names_of_enum')) {
    /**
     * Get the name of an enum.
     *
     * @param class-string $enum
     * @return list<string>
     */
    function names_of_enum(string $enum): array
    {
        return array_map(fn ($e) => $e->name, $enum::cases());
    }
}