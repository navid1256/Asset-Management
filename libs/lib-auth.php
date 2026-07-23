<?php

function normalizeDigitsToEnglish(string $value): string
{
    return strtr($value, [
        '۰' => '0',
        '۱' => '1',
        '۲' => '2',
        '۳' => '3',
        '۴' => '4',
        '۵' => '5',
        '۶' => '6',
        '۷' => '7',
        '۸' => '8',
        '۹' => '9',
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
    ]);
}

function findUserByUsername(PDO $pdo, string $username): ?array
{
    $statement = $pdo->prepare(
        'SELECT
            users.id,
            users.national_id,
            users.first_name,
            users.last_name,
            user_credentials.username,
            user_credentials.password_hash
         FROM users
         INNER JOIN user_credentials
            ON user_credentials.national_id = users.national_id
         WHERE user_credentials.username = :username
         LIMIT 1'
    );

    $statement->execute([
        'username' => $username,
    ]);

    $user = $statement->fetch();

    return $user ?: null;
}

function authenticateUser(PDO $pdo, string $username, string $password): ?array
{
    $user = findUserByUsername($pdo, $username);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    unset($user['password_hash']);

    return $user;
}

function findUserById(PDO $pdo, int $userId): ?array
{
    $statement = $pdo->prepare(
        'SELECT
            users.id,
            users.national_id,
            users.first_name,
            users.last_name,
            user_credentials.username
         FROM users
         INNER JOIN user_credentials
            ON user_credentials.national_id = users.national_id
         WHERE users.id = :user_id
         LIMIT 1'
    );

    $statement->execute([
        'user_id' => $userId,
    ]);

    $user = $statement->fetch();

    return $user ?: null;
}

function createUser(PDO $pdo, string $nationalId, string $firstName, string $lastName, string $username, string $password): int 
{
    $nationalId = normalizeDigitsToEnglish(trim($nationalId));
    $firstName = trim($firstName);
    $lastName = trim($lastName);
    $username = trim($username);

    if (!preg_match('/^[0-9]{10}$/', $nationalId)) {
        throw new InvalidArgumentException('کد ملی باید دقیقاً ۱۰ رقم باشد.');
    }

    if ($firstName === '' || $lastName === '' || $username === '') {
        throw new InvalidArgumentException('تمام فیلدها الزامی هستند.');
    }

    if (preg_match('/[0-9۰-۹٠-٩]/u', $firstName . $lastName)) {
        throw new InvalidArgumentException('نام و نام خانوادگی نباید شامل عدد باشند.');
    }

    if (mb_strlen($password) < 8) {
        throw new InvalidArgumentException('رمز عبور باید حداقل ۸ کاراکتر باشد.');
    }

    $pdo->beginTransaction();

    try {
        $userStatement = $pdo->prepare(
            'INSERT INTO users (national_id, first_name, last_name)
             VALUES (:national_id, :first_name, :last_name)'
        );

        $userStatement->execute([
            'national_id' => $nationalId,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        $userId = (int) $pdo->lastInsertId();

        $credentialStatement = $pdo->prepare(
            'INSERT INTO user_credentials
                (national_id, username, password_hash)
             VALUES
                (:national_id, :username, :password_hash)'
        );

        $credentialStatement->execute([
            'national_id' => $nationalId,
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $pdo->commit();

        return $userId;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}
