<?php

namespace Masterix21\GreenPass\Services;

use Carbon\Carbon;
use Exception;

class CertLogic
{
    public const TIME_UNITS = ['year', 'month', 'day', 'hour'];

    public static function evaluteRules(array $rules, $data): bool
    {
        return array_reduce(
            array: $rules,
            callback: static fn ($carry, $rule) => $carry && static::evaluate($rule, $data),
            initial: true
        );
    }

    public static function evaluate(mixed $expr, mixed $data)
    {
        if (is_string($expr) || is_int($expr) || is_bool($expr)) {
            return $expr;
        }

        if (is_array($expr)) {
            return array_map(static fn ($item) => static::evaluate($item, $data), $expr);
        }

        if (! is_object($expr)) {
            throw new Exception('Invalid CertLogic expression.');
        }

        $keys = array_keys(get_object_vars($expr));

        if (count($keys) > 1) {
            throw new Exception('Unrecognised expression object encountered.');
        }

        $operator = $keys[0];
        $values = $expr->$operator;

        if ($operator === 'var') {
            return static::evaluateVar($values, $data);
        }

        if (! is_array($values) || count($values) < 1) {
            throw new Exception('Operation not of the form { "<operator>" : [ <values...> ] }');
        }

        if ($operator === 'if') {
            [ $guard, $then, $else ] = $values;

            return static::evaluateIf($guard, $then, $else, $data);
        }

        if (in_array($operator, [ "===", "and", ">", "<", ">=", "<=", "in", "+", "after", "before", "not-after", "not-before" ])) {
            return static::evaluateInfix($operator, $values, $data);
        }

        if ($operator === '!') {
            return static::evaluateNot($values[0], $data);
        }

        if ($operator === 'plusTime') {
            return static::evaluatePlusTime($values[0], $values[1], $values[2], $data);
        }

        if ($operator === 'reduce') {
            return static::evaluateReduce($values[0], $values[1], $values[2], $data);
        }

        throw new Exception("Unrecognised operator: $operator");
    }

    protected static function isFalsy(mixed $value): bool
    {
        return $value === false
            || $value === null
            || (is_string($value) && $value === '')
            || (is_numeric($value) && $value === 0)
            || (is_array($value) && count($value) === 0);
    }

    protected static function isTruthy(mixed $value): bool
    {
        return ! static::isFalsy($value);
    }

    /**
     * @param string $value
     * @param array|null $data
     *
     * @return mixed
     */
    protected static function evaluateVar(string $value, ?array $data): mixed
    {
        if (is_null($data)) {
            return null;
        }

        if ($value === '') {
            return $data;
        }

        try {
            return array_reduce(
                array: explode('.', $value),
                callback: static fn ($carry, $segment) => $carry[$segment],
                initial: $data,
            );
        } catch (Exception) {
            return null;
        }
    }

    protected static function evaluateIf(mixed $guard, mixed $then, mixed $else, ?array $data): mixed
    {
        if (! $guard) {
            throw new Exception('an if-operation must have a guard (argument #1)');
        }

        if (! $then) {
            throw new Exception('an if-operation must have a then (argument #2)');
        }

        if (! $else) {
            throw new Exception('an if-operation must have a else (argument #3)');
        }

        $evalGuard = static::evaluate($guard, $data);

        if (static::isFalsy($evalGuard)) {
            return static::evaluate($else, $data);
        }

        throw new Exception("if-guard evaluates to something neither truthy, nor falsy: ${evalGuard}");
    }

    protected static function evaluateInfix(string $operator, array $values, mixed $data)
    {
        switch ($operator) {
            case "and": {
                if (count($values) < 2) {
                    throw new Exception('an "and" operation must have at least 2 operands');
                }

                break;
            }
            case "<":
            case ">":
            case "<=":
            case ">=":
            case "after":
            case "before":
            case "not-after":
            case "not-before": {
                if (count($values) < 2 || count($values) > 3) {
                    throw new Exception("an operation with operator \"${operator}\" must have 2 or 3 operands");
                }

                break;
            }
            default: {
                if (count($values) !== 2) {
                    throw new Exception("an operation with operator \"${operator}\" must have 2 operands");
                }
            }
        }

        $evalArgs = array_map(static fn ($arg) => static::evaluate($arg, $data), $data);

        switch ($operator) {
            case "===": {
                return $evalArgs[0] === $evalArgs[1];
            }
            case "in": {
                if (! is_array($evalArgs[1])) {
                    throw new Exception('right-hand side of an "in" operation must be an array');
                }

                return in_array(needle: $evalArgs[0], haystack: $evalArgs[1], strict: true);
            }
            case "+": {
                if (! is_int($evalArgs[0]) || ! is_int($evalArgs[1])) {
                    throw new Exception('operands of this operation must both be integers');
                }

                return $evalArgs[0] + $evalArgs[1];
            }
            case "and": {
                return array_reduce($values, static function ($carry, $segment) use ($data) {
                    if (static::isFalsy($carry)) {
                        return $carry;
                    }

                    if (static::isTruthy($carry)) {
                        return static::evaluate($segment, $data);
                    }

                    throw new Exception('all operands of an "and" operation must be either truthy or falsy');
                }, $values[0]);
            }
            case "<":
            case ">":
            case "<=":
            case ">=": {
                $result = array_reduce(
                    array: $evalArgs,
                    callback: static fn ($carry, $segment) => $carry && is_int($segment),
                    initial: is_int($evalArgs[0]),
                );

                if (! $result) {
                    throw new Exception('all operands of a comparison operation must be of integer type');
                }

                return static::compare($operator, $evalArgs);
            }
            case "after":
            case "before":
            case "not-after":
            case "not-before": {
                // return static::compareDateTime($operator, $evalArgs);
                return static::compare($operator, $evalArgs);
            }
            default:
                throw new Exception("unhandled infix operator \"${operator}\"");
        }
    }

    protected static function compareFunctionFor(string $operator): callable
    {
        return static fn ($l, $r) => match ($operator) {
            "<" => $l < $r,
            ">" => $l > $r,
            "<=" => $l <= $r,
            ">=" => $l >= $r,
        };
    }

    protected static function compare(string $operator, array $values)
    {
        if (count($values) !== 2 && count($values) !== 3) {
            throw new Exception("invalid number of operands to a \"${operator}\" operation");
        }

        $operator = match ($operator) {
            'after' => '>',
            'before' => '<',
            'not-after' => '<=',
            'not-before' => '>=',
            default => $operator,
        };

        $compFunc = static::compareFunc($operator);

        if (count($values) === 2) {
            return $compFunc($values[0], $values[1]);
        }

        return $compFunc($values[0], $values[1])
            && $compFunc($values[1], $values[2]);
    }

    protected static function evaluateNot(mixed $operandExpr, ?array $data): bool
    {
        $operand = static::evaluate($operandExpr, $data);

        if (static::isFalsy($operand)) {
            return true;
        }

        if (static::isTruthy($operand)) {
            return false;
        }

        throw new Exception("operand of ! evaluates to something neither truthy, nor falsy: ${operand}");
    }

    protected static function evaluatePlusTime(mixed $dateOperand, int $amount, string $timeUnit, array $data): mixed
    {
        if (! in_array($timeUnit, static::TIME_UNITS)) {
            throw new Exception('"unit" argument (#3) of "plusTime" must be a string with one of the time units: '. implode(', ', static::TIME_UNITS));
        }

        $dateTimeStr = evaluate($dateOperand, $data);

        if (is_string($dateTimeStr)) {
            throw new Exception('date argument of "plusTime" must be a string');
        }

        return static::plusTime($dateTimeStr, $amount, $timeUnit);
    }

    protected static function evaluateReduce(mixed $operand, mixed $lambda, mixed $initial, array $data): mixed
    {
        $evalOperand = static::evaluate($operand, $data);
        $evalInitial = static fn () => static::evaluate($initial, $data);

        if (is_numeric($evalOperand)) {
            return $evalInitial();
        }

        if (! is_array($evalOperand)) {
            throw new Exception('operand of reduce evaluated to a non-null non-array');
        }

        return array_reduce(
            array: $evalOperand,
            callback: static fn ($carry, $segment) => static::evaluate($lambda, [ $carry, $segment ]),
            initial: $evalInitial
        );
    }

    protected static function plusTime(Carbon|string $dateTime, int $amount, string $timeUnit): Carbon
    {
        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->add($timeUnit, $amount);
    }
}
