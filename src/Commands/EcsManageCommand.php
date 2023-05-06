<?php

namespace Sfolador\EcsManage\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Sfolador\EcsManage\Facades\EcsManage;
use Sfolador\EcsManage\Terminals\ITerm;
use Sfolador\EcsManage\Terminals\LinuxTerminal;
use Sfolador\EcsManage\Terminals\Terminal;

class EcsManageCommand extends Command
{
    public $signature = 'ecs:manage';

    public $description = 'Manage your ecs cluster';

    public function handle(): int
    {
        EcsManage::listClusters();

        if (EcsManage::getClusters()->isEmpty()) {
            $this->error('No clusters found');

            return self::FAILURE;
        }

        $chooseCluster = $this->choice('Select a cluster', EcsManage::getClusters()->toArray(), EcsManage::getClusters()->first());

        /** @phpstan-ignore-next-line  */
        EcsManage::selectCluster($chooseCluster);
        EcsManage::listServicesForCluster();

        /** @phpstan-ignore-next-line  */
        $environment = $this->choice('Select an environment', EcsManage::getEnvironments());
        /** @phpstan-ignore-next-line  */
        EcsManage::filterServices($environment);

        if (EcsManage::serviceNames()->isEmpty()) {
            $this->error('No services found');

            return self::FAILURE;
        }

        $service = $this->choice('Select a service', EcsManage::serviceNames()->toArray(), EcsManage::serviceNames()->first());

        /** @phpstan-ignore-next-line  */
        EcsManage::selectService($service);
        EcsManage::listTaskDefinitions();

        if (EcsManage::taskDefinitionNames()->isEmpty()) {
            $this->error('No task definitions found');

            return self::FAILURE;
        }

        $task = $this->choice('Select a task', EcsManage::taskDefinitionNames()->toArray(), EcsManage::taskDefinitionNames()->first());

        /** @phpstan-ignore-next-line  */
        EcsManage::selectTask($task);

        $command = EcsManage::createCommand();

        $defaultTerminal = config('ecs-manage.default_terminal', 'iTerm');

        $openTerminal = match ($defaultTerminal) {
            default => ITerm::open($command),
            'Terminal' => Terminal::open($command),
            'LinuxTerminal' => LinuxTerminal::open($command),
        };

        $run = Process::run($openTerminal);
        echo $run->output();

        return self::SUCCESS;
    }
}
