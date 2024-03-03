<?php

function piket_compare($value1, $value2, $precision = 15)
{
    $value1 = sprintf('%.'.$precision.'f', $value1);
    $value2 = sprintf('%.'.$precision.'f', $value2);
    return intval(bccomp($value1, $value2, $precision));
}

function piket_add($value1, $value2, $precision = 15)
{
    $value1 = sprintf('%.'.$precision.'f', $value1);
    $value2 = sprintf('%.'.$precision.'f', $value2);
    return floatval(bcadd($value1, $value2, $precision));
}

function piket_add_str($value1, $value2, $precision = 15)
{
    $value1 = sprintf('%.'.$precision.'f', $value1);
    $value2 = sprintf('%.'.$precision.'f', $value2);
    return bcadd($value1, $value2, $precision);
}

function piket_sub($value1, $value2, $precision = 15)
{
    $value1 = sprintf('%.'.$precision.'f', $value1);
    $value2 = sprintf('%.'.$precision.'f', $value2);
    return floatval(bcsub($value1, $value2, $precision));
}

function piket_multiply($value1, $value2, $precision = 15)
{
    $value1 = sprintf('%.'.$precision.'f', $value1);
    $value2 = sprintf('%.'.$precision.'f', $value2);
    return floatval(bcmul($value1, $value2, $precision));
}

function piket_floor($amount, $precision = 15)
{
    $mul = pow(10, $precision);
    $amount = floor($amount*$mul)/$mul;
    return sprintf('%.'.$precision.'f', $amount);
}

function piket_ceil($amount, $precision = 15)
{
    $mul = pow(10, $precision);
    $amount = ceil($amount*$mul)/$mul;
    return sprintf('%.'.$precision.'f', $amount);
}