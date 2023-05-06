<?php

it('can generate a command to open terminal', function () {

    $command = Sfolador\EcsManage\Terminals\Terminal::open('ls -la');
    $this->assertEquals('osascript -e "tell application \"Terminal\" to do script \"ls -la\" in front window"', $command);

});
