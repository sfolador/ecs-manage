<?php

use Aws\Ecs\EcsClient;
use Illuminate\Console\Command;
use Sfolador\EcsManage\Facades\EcsManage;
use Sfolador\EcsManage\Terminals\ITerm;

beforeEach(function () {
    $clusters = collect([
        'clusterArns' => [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster-2',
        ],
    ]);
    $this->client = \Pest\Laravel\mock(EcsClient::class)->shouldReceive('listClusters')->andReturn($clusters)->getMock();

    EcsManage::setClient($this->client);

    $services = collect([
        'serviceArns' => [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/service-name',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/service-name-2',
        ],
    ]);

    $tasks = collect([
        'taskArns' => [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/task-1',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/task-2',
        ],
    ]);

    EcsManage::listClusters();
    $selectedCluster = 'ecs-cli-demo-cluster';
    EcsManage::selectCluster($selectedCluster);

    $this->client->shouldReceive('listServices')
        ->with(['cluster' => 'ecs-cli-demo-cluster', 'maxResults' => 100])
        ->andReturn($services);

    $selectedService = 'service-name';
    EcsManage::selectService($selectedService);

    $this->client->shouldReceive('listTasks')
        ->with(['serviceName' => $selectedService, 'cluster' => $selectedCluster])
        ->andReturn($tasks);

    EcsManage::listTaskDefinitions();

    $names = EcsManage::taskDefinitionNames();

    $selectedTask = 'task-1';
    EcsManage::selectTask($selectedTask);

});

it('asks to select a cluster', function () {

    config()->set('ecs-manage.environments', [
        'service',
    ]);

    $this->artisan('ecs:manage')
        ->expectsQuestion('Select a cluster', 'ecs-cli-demo-cluster')
        ->expectsQuestion('Select an environment', 'service')
        ->expectsQuestion('Select a service', 'service-name')
        ->expectsQuestion('Select a task', 'task-1');

    $command = ITerm::open(EcsManage::createCommand());

    \Illuminate\Support\Facades\Process::shouldReceive('run')
        ->with($command);
});

it('exits if no clusters', function () {

    config()->set('ecs-manage.environments', [
        'service',
    ]);

    EcsManage::shouldReceive('listClusters');
    EcsManage::shouldReceive('getClusters')
        ->andReturn(collect());

    $this->artisan('ecs:manage')
        ->assertExitCode(Command::FAILURE);

});

it('exits if no serviceNames', function () {

    config()->set('ecs-manage.environments', [
        'service',
    ]);

    EcsManage::shouldReceive('listClusters');
    EcsManage::shouldReceive('getClusters')->andReturn(collect(
        [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster-2',
        ]
    ));
    EcsManage::shouldReceive('selectCluster')->with('ecs-cli-demo-cluster');
    EcsManage::shouldReceive('listServicesForCluster');
    EcsManage::shouldReceive('getEnvironments')->andReturn(['service']);
    EcsManage::shouldReceive('filterServices');
    EcsManage::shouldReceive('serviceNames')
        ->andReturn(collect());

    $this->artisan('ecs:manage')
        ->expectsQuestion('Select a cluster', 'ecs-cli-demo-cluster')
        ->expectsQuestion('Select an environment', 'service')
        ->assertExitCode(Command::FAILURE);

});

it('exits if no tasks', function () {

    config()->set('ecs-manage.environments', [
        'service',
    ]);

    EcsManage::shouldReceive('listClusters');
    EcsManage::shouldReceive('getClusters')->andReturn(collect(
        [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster-2',
        ]
    ));
    EcsManage::shouldReceive('selectCluster')->with('ecs-cli-demo-cluster');
    EcsManage::shouldReceive('listServicesForCluster');
    EcsManage::shouldReceive('getEnvironments')->andReturn(['service']);
    EcsManage::shouldReceive('filterServices');
    EcsManage::shouldReceive('serviceNames')->andReturn(collect(['service-name']));
    EcsManage::shouldReceive('selectService');
    EcsManage::shouldReceive('listTaskDefinitions');
    EcsManage::shouldReceive('taskDefinitionNames')->andReturn(collect());

    $this->artisan('ecs:manage')
        ->expectsQuestion('Select a cluster', 'ecs-cli-demo-cluster')
        ->expectsQuestion('Select an environment', 'service')
        ->expectsQuestion('Select a service', 'service-name')
        ->assertExitCode(Command::FAILURE);

});
