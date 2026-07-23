<?php

function getDeputies(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT id, name
         FROM deputies
         ORDER BY name'
    );

    return $statement->fetchAll();
}

function getDepartments(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT id, deputy_id, name
         FROM departments
         ORDER BY deputy_id, name'
    );

    return $statement->fetchAll();
}

function departmentBelongsToDeputy(PDO $pdo, int $departmentId, int $deputyId): bool
{
    $statement = $pdo->prepare(
        'SELECT 1
         FROM departments
         WHERE id = :department_id
           AND deputy_id = :deputy_id
         LIMIT 1'
    );

    $statement->execute([
        'department_id' => $departmentId,
        'deputy_id' => $deputyId,
    ]);

    return (bool) $statement->fetchColumn();
}

function saveEmployee(
    PDO $pdo,
    string $firstName,
    string $lastName,
    string $nationalId,
    string $mobile,
    int $deputyId,
    int $departmentId
): int {
    $statement = $pdo->prepare(
        'INSERT INTO employees (
            first_name,
            last_name,
            national_id,
            mobile,
            deputy_id,
            department_id
         ) VALUES (
            :first_name,
            :last_name,
            :national_id,
            :mobile,
            :deputy_id,
            :department_id
         )
         ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            mobile = VALUES(mobile),
            deputy_id = VALUES(deputy_id),
            department_id = VALUES(department_id),
            id = LAST_INSERT_ID(id)'
    );

    $statement->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'national_id' => $nationalId,
        'mobile' => $mobile,
        'deputy_id' => $deputyId,
        'department_id' => $departmentId,
    ]);

    return (int) $pdo->lastInsertId();
}

function findEmployeeById(PDO $pdo, int $employeeId): ?array
{
    $statement = $pdo->prepare(
        'SELECT
            employees.id,
            employees.first_name,
            employees.last_name,
            employees.national_id,
            employees.mobile,
            employees.deputy_id,
            employees.department_id,
            deputies.name AS deputy_name,
            departments.name AS department_name
         FROM employees
         INNER JOIN deputies
            ON deputies.id = employees.deputy_id
         INNER JOIN departments
            ON departments.id = employees.department_id
           AND departments.deputy_id = employees.deputy_id
         WHERE employees.id = :employee_id
         LIMIT 1'
    );

    $statement->execute([
        'employee_id' => $employeeId,
    ]);

    $employee = $statement->fetch();

    return $employee ?: null;
}
