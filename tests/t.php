<?php

class Tests
{
    public function lal()
    {
        try {
            echo test
        } catch (\Error $e) {
            file_put_contents(__DIR__ . '/error.log', $e->getMessage();
        }
    }
}

try {

    (new Tests)->lal();

    call_user_func(function () {
        throw new \Exception('开会呀');
    });
} catch (\Error $e) {
    file_put_contents(__DIR__ . '/error.log', $e->getMessage();
} catch (\Throwable $e) {
    file_put_contents(__DIR__ . '/exception.log', $e->getMessage());
}