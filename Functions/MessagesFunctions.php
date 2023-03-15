<?php

namespace Egur\Functions;


/**
 * Выполняет функцию die() с выводом json encoded error
 * @param string $error
 * @return never
 */
function die_with_error(string $error) 
{
    die(json_encode(['error' => $error]));
}

/**
 * Выполняет функцию echo() с выводом json encoded error
 * @param string $error
 * @return never
 */
function echo_error(string $error) 
{
    die(json_encode(['error' => $error]));
}

/**
 * Возвращает строку с json encoded error
 * @param string $error
 * @return string
 */
function get_error(string $error) 
{
    return json_encode(['error' => $error]);
}

/**
 * Выполняет функцию die() с выводом json encoded message
 * @param string $message
 * @return never
 */
function die_with_message(string $message) 
{
    die(json_encode(['message' => $message]));
}

/**
 * Выполняет функцию echo() с выводом json encoded message
 * @param string $message
 * @return never
 */
function echo_message(string $message) 
{
    die(json_encode(['message' => $message]));
}

/**
 * Возвращает строку с json encoded message
 * @param string $message
 * @return bool|string
 */
function get_message(string $message) 
{
    return json_encode(['message' => $message]);
}