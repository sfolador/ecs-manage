<?php

namespace Sfolador\EcsManage\Terminals;

class ITerm
{
    public static function open($command)
    {
        return 'osascript -e "
      tell application \"iTerm2\"
        set newWindow to (create window with default profile)
        tell current session of newWindow
            write text \"'.$command.' \"
        end tell
      end tell
    "';

    }
}
