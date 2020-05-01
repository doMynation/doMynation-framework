<?php

declare(strict_types=1);

namespace Domynation\Database;

/**
 * A simplified and more efficient version of `DatabaseFilterMapper`.
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class DatabaseFilterMapperSimplified implements DatabaseFilterMapperInterface
{
    /**
     * A mapping from a filters name to a class.
     */
    private array $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $filters): array
    {
        $instances = [];

        foreach ($filters as $name => $value) {
            if (!isset($this->mapping[$name])) {
                continue;
            }

            $className = $this->mapping[$name];

            // Call `validate` if it exists
            $isValid = call_user_func_array($className . '::validate', [$value]);

            if ($isValid) {
                $instances[] = call_user_func_array($className . '::fromForm', [$value]);
            }
        }

        return $instances;
    }
}