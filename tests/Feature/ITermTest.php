<?php

it('can generate a command to open iterm', function () {

    $command = Sfolador\EcsManage\Terminals\ITerm::open('ls -la');
    $this->assertEquals('osascript -e "
      tell application \"iTerm2\"
        set newWindow to (create window with default profile)
        tell current session of newWindow
            write text \"ls -la \"
        end tell
      end tell
    "', $command);

});

it('can generate a command to open terminal', function () {

    $command = Sfolador\EcsManage\Terminals\Terminal::open('ls -la');
    $this->assertEquals('osascript -e "tell application \"Terminal\" to do script \"ls -la\" in front window"', $command);

});

it('can generate a command to open terminal on ubuntu', function () {

    $command = Sfolador\EcsManage\Terminals\LinuxTerminal::open('ls -la');
    $this->assertEquals('gnome-terminal -- ls -la', $command);
});
