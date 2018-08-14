<?php

function readEnv(string $prefix, string ...$keys): array
{
    return array_map(function (string $key) use ($prefix) {
        return getenv($prefix . $key);
    }, $keys);
}

function writeln(string $message, ...$args): void
{
    echo sprintf("$message\n", ...$args);
}

function connect(string $dbname, string $host, string $port, string $user, string $password): \PDO
{
    do {
        try {
            $connection = "pgsql:dbname=$dbname;host=$host;port=$port";
            writeln('connecting to "%s" ...', $connection);

            $db = new \PDO($connection, $user, $password);
            writeln('connected');
        } catch (\Exception $e) {
            writeln('waiting for db ...');
            usleep(300 * 1000);
            $db = null;
        }
    } while ($db === null);

    return $db;
}

function query(\PDO $connection)
{
    return function (string $sql) use ($connection) {
        $stmt = $connection->query($sql);

        if ($stmt === false) {
            writeln('SQL: "%s" is false', $sql);

            return $stmt;
        }

        $stmt->execute();

        return $stmt;
    };
}

function fetchAll(callable $query)
{
    return function ($sql) use ($query): array {
        return $query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    };
}

function tryDo(callable $do, int $waitFor = 1000)
{
    try {
        $do();
    } catch (\Throwable $e) {
        writeln('ERROR: %s', $e->getMessage());
    }
    usleep($waitFor * 1000);
}

writeln('===========================');
writeln('.......... Start ..........');

$query = query(connect(...readEnv('DB_', 'NAME', 'HOST', 'PORT', 'USER', 'PASS')));
$fetch = fetchAll($query);

tryDo(function () use ($query) {
    writeln('creating db...');
    $query('
        CREATE TABLE movie (
            id SERIAL NOT NULL,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )
    ');
    writeln('db is created');
});

tryDo(function () use ($query) {
    writeln('inserting movies...');
    $query("INSERT INTO movie (name) VALUES ('Kill Bill'), ('Kill Bill 2')");
    writeln('movies are inserted');
});

tryDo(function () use ($fetch) {
    writeln('reading movies ...');
    $movies = array_map(function (array $movie) {
        return sprintf('%s | %s', $movie['id'], $movie['name']);
    }, $fetch('SELECT * FROM movie'));

    foreach($movies as $movie) {
        writeln('- %s', $movie);
    }
});

writeln('.......... END ..........');
writeln('=========================');
