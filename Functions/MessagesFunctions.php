<?php

namespace Egur\Functions;


/**
 * Выполняет функцию die() с выводом json encoded error
 * 
 * @param string $error
 */
function die_with_error(string $error): void
{
    if (is_string($error))
        die(json_encode(['error' => $error]));
    else
        die(json_encode(['error' => 'uncaught error']));
}

/**
 * Выполняет функцию echo() с выводом json encoded error
 * 
 * @param string $error
 */
function echo_error(string $error): void
{
    if (is_string($error))
        echo(json_encode(['error' => $error]));
    else
        echo(json_encode(['message' => 'uncaught error']));
}

/**
 * Возвращает строку с json encoded error
 * 
 * @param string $error
 * @return string
 */
function get_error(string $error): string
{
    if (is_string($error))
        return json_encode(['error' => $error]);
    else
        return json_encode(['message' => 'uncaught error']);
}

/**
 * Выполняет функцию die() с выводом json encoded message
 * 
 * @param string $message
 */
function die_with_message(string $message): void
{
    if (is_string($message))
        die(json_encode(['message' => $message]));
    else
        die(json_encode(['message' => 'uncaught error']));
}

/**
 * Выполняет функцию echo() с выводом json encoded message
 * 
 * @param string $message
 */
function echo_message(string $message): void
{
    if (is_string($message))
        echo(json_encode(['message' => $message]));
    else
        echo(json_encode(['message' => 'uncaught error']));
}

/**
 * Возвращает строку с json encoded message
 * 
 * @param string $message
 * @return string
 */
function get_message(string $message = ''): string
{
    if (is_string($message))
        return json_encode(['message' => $message]);
    else
        return json_encode(['message' => 'uncaught error']);
}