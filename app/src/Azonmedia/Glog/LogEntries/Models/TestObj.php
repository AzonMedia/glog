<?php


namespace Azonmedia\Glog\LogEntries\Models;


use Guzaba2\Coroutine\Coroutine;

class TestObj
{
    public function __construct()
    {
        print_r(Coroutine::getFullBacktrace(NULL, \DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    public function m1() {
        print_r(Coroutine::getFullBacktrace(NULL, \DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    public static function m2() {
        print '666666666';
        //print_r(Coroutine::getFullBacktrace(NULL, \DEBUG_BACKTRACE_IGNORE_ARGS));
    }
}