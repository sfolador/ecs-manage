<?php

namespace Sfolador\EcsManage\Terminals;

class Terminal
{
    public static function open($command): string
    {
        return 'osascript -e "tell application \"Terminal\" to do script \"'.$command.'\" in front window"';
    }
}
