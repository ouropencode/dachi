<?php

namespace Dachi\Tests;

use Dachi\Core\Session;

class SessionTest extends Dachi_TestBase
{
    public function testSessionStart()
    {
        $this->assertEquals(PHP_SESSION_NONE, session_status());

        $previousSessionID = session_id();
        $this->assertEquals('', session_id());

        Session::start('DachiTest');

        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        $this->assertNotEquals('', session_id());
        $this->assertNotEquals($previousSessionID, session_id());
    }

    public function testHasSessionMoved()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'DachiTest/2.0-stage1';
        Session::start('DachiTest');

        $this->assertFalse(Session::hasSessionMoved());

        $_SERVER['HTTP_USER_AGENT'] = 'DachiTest/2.0-stage2';

        $this->assertTrue(Session::hasSessionMoved());
    }

    public function testHasSessionMovedInternetExplorer8Patch()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30)';
        Session::start('DachiTest');

        $this->assertFalse(Session::hasSessionMoved());

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30)';
        $this->assertFalse(Session::hasSessionMoved());
    }

    public function testSessionDestroyWhenMovedUserAgent()
    {
        $random = mt_rand(1, 9999999);

        $_SERVER['HTTP_USER_AGENT'] = 'DachiTest/2.0-stage1';
        Session::start('DachiTest');
        $_SESSION['test_variable'] = $random;

        $_SERVER['HTTP_USER_AGENT'] = 'DachiTest/2.0-stage2';
        Session::start('DachiTest');

        $this->assertArrayNotHasKey('test_variable', $_SESSION);
    }

    public function testSessionIdRegeneration()
    {
        $random = mt_rand(1, 9999999);

        Session::start('DachiTest');
        $_SESSION['test_variable'] = $random;

        $previousSessionID = session_id();
        Session::regenerate();
        $this->assertNotEquals($previousSessionID, session_id());

        $this->assertArrayHasKey('test_variable', $_SESSION);
        $this->assertEquals($random, $_SESSION['test_variable']);
    }

    public function testDoesInvalidate()
    {
        Session::start('DachiTest');

        $this->assertTrue(Session::isValid());

        $_SESSION['dachi_closed'] = true;
        $this->assertFalse(Session::isValid());

        $_SESSION['dachi_closed'] = true;
        $_SESSION['dachi_expires'] = time() + 3;
        $this->assertTrue(Session::isValid());

        $_SESSION['dachi_expires'] = time() - 3;
        $this->assertFalse(Session::isValid());

        unset($_SESSION['dachi_closed']);
        unset($_SESSION['dachi_expires']);
        $this->assertTrue(Session::isValid());
    }
}
