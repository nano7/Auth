<?php namespace Nano7\Auth;

class ConsoleGuard extends Guard
{
    /**
     * @return UserInterface|null
     */
    protected function retrieve()
    {
        return null;
    }
}