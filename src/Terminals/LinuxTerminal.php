<?php

namespace Sfolador\EcsManage\Terminals;

class LinuxTerminal
{
    public static function open($command)
    {
       return "gnome-terminal -- ".$command;

    }
}
