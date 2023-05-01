<?php

namespace Sfolador\EcsManage;

use Aws\AwsClientInterface;
use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EcsManage
{
    public const STAGING_ENVIRONMENT = 'staging';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public Collection $clusters;

    public Collection $services;

    public Collection $filteredServices;

    public Collection $taskDefinitions;

    public string $selectedCluster;

    public string $selectedService;

    public string $selectedTask;

    public array $defaultOptions;

    private AwsClientInterface $client;

    public function __construct()
    {
        $this->clusters = collect();
        $this->services = collect();
        $this->filteredServices = collect();
        $this->taskDefinitions = collect();

        $this->selectedCluster = '';
        $this->selectedService = '';
        $this->selectedTask = '';

        $this->defaultOptions = [
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'version' => '2014-11-13',
        ];

        $this->client = AWS::createClient('ecs', $this->defaultOptions);
    }

    public function getEnvironments()
    {
        return config('ecs-manage.environments', [self::STAGING_ENVIRONMENT, self::PRODUCTION_ENVIRONMENT]);
    }

    public function listClusters(): static
    {
        $this->clusters = collect(
            $this->client
                ->listClusters()
                ->get('clusterArns')
        );

        return $this;

    }

    public function getClusters(): Collection
    {
        return $this->clusters->map(fn ($cluster) => Str::after($cluster, 'cluster/'));
    }

    public function selectCluster($clusterName): static
    {
        $this->selectedCluster = $clusterName;

        return $this;
    }

    public function listServicesForCluster(string $clusterName = null): static
    {
        if ($clusterName) {
            $this->selectCluster($clusterName);
        }

        $this->services = collect(
            $this->client
                ->listServices(['cluster' => $this->selectedCluster, 'maxResults' => 100])
                ->get('serviceArns')
        );

        return $this;

    }

    public function filterServices(string $filterWord): static
    {
        $serviceNames = $this->services->map(fn ($service) => Str::after($service, $this->selectedCluster.'/'));

        $this->filteredServices = $serviceNames->filter(fn ($service) => Str::contains($service, $filterWord))->sort()->values();

        return $this;
    }

    public function serviceNames(): Collection
    {
        return $this->filteredServices;
    }

    public function selectService(string $serviceName): static
    {
        $this->selectedService = $serviceName;

        return $this;
    }

    public function listTaskDefinitions(): static
    {
        $this->taskDefinitions = collect($this->client->listTasks(['serviceName' => $this->selectedService, 'cluster' => $this->selectedCluster])->get('taskArns'));

        return $this;
    }

    public function taskDefinitionNames(): Collection
    {
        return $this->taskDefinitions->map(fn ($service) => Str::after($service, $this->selectedCluster.'/'));
    }

    public function selectTask(string $taskName): static
    {
        $this->selectedTask = $taskName;

        return $this;
    }

    public function createCommand(): string
    {
        return "aws ecs execute-command --region  eu-west-3 --cluster $this->selectedCluster --task $this->selectedTask --command \"/bin/sh\" --interactive";
    }
}
