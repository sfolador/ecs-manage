<?php

it('can generate a command to open terminal on ubuntu', function () {

    $command = Sfolador\EcsManage\Terminals\LinuxTerminal::open('ls -la');
    $this->assertEquals('gnome-terminal -- ls -la', $command);
});
