<?php

namespace Sfolador\EcsManage\Terminals;

class LinuxTerminal
{
    public static function open(string $command): string
    {
        return 'gnome-terminal -- '.$command;

    }
}
