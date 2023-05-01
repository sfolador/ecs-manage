<?php

// config for Sfolador/EcsManage
return [

    /**
     * This list provides a "filter" used in the ECS Manage command. You can change the words
     * in this list to match your own environment names. For example, if you have a
     * "test" and "nice" environment, you could change this to:
     * [
     *  'test',
     *  'nice',
     * ]
     */
    'environments' => [
        'staging',
        'production',
    ],

    /**
     * The default terminal to use when opening a terminal window. This can be
     * iTerm, Terminal, or LinuxTerminal. LinuxTerminal is run assuming
     * that gnome-terminal is installed.
     */
    'default_terminal' => 'iTerm'
];
