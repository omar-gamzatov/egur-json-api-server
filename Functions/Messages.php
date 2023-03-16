<?php

namespace Egur\Functions;

class Messages
{
    /**
     * Выполняет функцию die() с выводом json encoded
     */
    public static function dieWithError(string $error): void
    {
        if (is_string($error)) {
            die(json_encode(['error' => $error]));
        } else {
            die(json_encode(['error' => 'uncaught error']));
        }
    }

    /**
     * Выполняет функцию echo() с выводом json encoded error
     *
     * @param string $error
     */
    public static function echoError(string $error): void
    {
        if (is_string($error)) {
            echo(json_encode(['error' => $error]));
        } else {
            echo(json_encode(['message' => 'uncaught error']));
        }
    }

    /**
     * Возвращает строку с json encoded error
     *
     * @param string $error
     * @return string
     */
    public static function getError(string $error): string
    {
        if (is_string($error)) {
            return json_encode(['error' => $error]);
        } else {
            return json_encode(['message' => 'uncaught error']);
        }
    }

    /**
     * Выполняет функцию die() с выводом json encoded message
     *
     * @param string $message
     */
    public static function dieWithMessage(string $message): void
    {
        if (is_string($message)) {
            die(json_encode(['message' => $message]));
        } else {
            die(json_encode(['message' => 'uncaught error']));
        }
    }

    /**
     * Выполняет функцию echo() с выводом json encoded message
     *
     * @param string $message
     */
    public static function echoMessage(string $message): void
    {
        if (is_string($message)) {
            echo(json_encode(['message' => $message]));
        } else {
            echo(json_encode(['message' => 'uncaught error']));
        }
    }

    /**
     * Возвращает строку с json encoded message
     *
     * @param string $message
     * @return string
     */
    public static function getMessage(string $message = ''): string
    {
        if (is_string($message)) {
            return json_encode(['message' => $message]);
        } else {
            return json_encode(['message' => 'uncaught error']);
        }
    }

    /**
     * Выполняет функцию echo() с выводом json encoded array
     *
     * @param string $array
     */
    public static function echoArray(array $array): void
    {
        if (is_array($array)) {
            echo(json_encode($array));
        } else {
            echo(json_encode(['message' => 'uncaught error']));
        }
    }
}
