<?php

use Aws\Ecs\EcsClient;
use Illuminate\Support\Collection;
use Sfolador\EcsManage\Facades\EcsManage;

beforeEach(function () {
    $clusters = collect([
        'clusterArns' => [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster-2',
        ],
    ]);
    $this->client = \Pest\Laravel\mock(EcsClient::class)->shouldReceive('listClusters')->andReturn($clusters)->getMock();

    EcsManage::setClient($this->client);
});

it('can get environments', function () {
    $environments = EcsManage::getEnvironments();
    expect($environments)->toHaveCount(2)
        ->and($environments)
        ->toBe([\Sfolador\EcsManage\EcsManage::STAGING_ENVIRONMENT, \Sfolador\EcsManage\EcsManage::PRODUCTION_ENVIRONMENT]);
});

it('reads environments from the config', function () {
    config()->set('ecs-manage.environments', ['test', 'nice']);
    $environments = EcsManage::getEnvironments();
    expect($environments)->toHaveCount(2)
        ->and($environments)
        ->toBe(['test', 'nice']);
});

it('can list clusters', function () {

    EcsManage::listClusters();

    expect(EcsManage::getClusters())
        ->toBeInstanceOf(Collection::class)
        ->and(EcsManage::getClusters())
        ->toHaveCount(2);

});

it('gets clusters names', function () {
    EcsManage::listClusters();
    expect(EcsManage::getClusters())
        ->toContain('ecs-cli-demo-cluster', 'ecs-cli-demo-cluster-2');
});

it('can filter services', function () {

    $services = collect([
        'serviceArns' => [
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/service-name',
            'arn:aws:ecs:us-east-1:123456789012:cluster/ecs-cli-demo-cluster/service-name-2',
        ],
    ]);

    EcsManage::listClusters();

    $this->client->shouldReceive('listServices')
        ->with(['cluster' => 'ecs-cli-demo-cluster', 'maxResults' => 100])
        ->andReturn($services);

    EcsManage::listServicesForCluster('ecs-cli-demo-cluster');

    $ecsManage = EcsManage::filterServices('service-name');

    expect($ecsManage->serviceNames())->toHaveCount(2);

});

it('can list tasks', function () {

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

    expect($names)->toHaveCount(2)->toContain('task-1', 'task-2');

});

it('can create a command', function () {
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

    $command = EcsManage::createCommand();

    expect($command)->toBe("aws ecs execute-command --region  eu-west-3 --cluster $selectedCluster --task $selectedTask --command \"/bin/sh\" --interactive");

});
