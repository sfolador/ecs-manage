<?php

namespace Sfolador\EcsManage;

use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EcsManage implements EcsManageInterface
{
    public const STAGING_ENVIRONMENT = 'staging';

    public const PRODUCTION_ENVIRONMENT = 'production';

    /**
     * @var Collection<int,string>
     */
    private Collection $clusters;

    /**
     * @var Collection<int,string>
     */
    private Collection $services;

    /**
     * @var Collection<int,string>
     */
    private Collection $filteredServices;

    /**
     * @var Collection<int,string>
     */
    private Collection $taskDefinitions;

    private string $selectedCluster;

    private string $selectedService;

    private string $selectedTask;

    private array $defaultOptions;

    private mixed $client;

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
            'region' => env('AWS_DEFAULT_REGION', 'awsd-east-1'),
            'version' => '2014-11-13',
        ];
        $c = AWS::createClient('ecs', $this->defaultOptions);
        $this->client = $c;
    }

    public function setClient(mixed $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getEnvironments(): mixed
    {
        return config('ecs-manage.environments', [self::STAGING_ENVIRONMENT, self::PRODUCTION_ENVIRONMENT]);
    }

    public function listClusters(): static
    {

        /** @phpstan-ignore-next-line */
        $this->clusters = collect(
            /** @phpstan-ignore-next-line */
            $this->client
                ->listClusters()
                ->get('clusterArns')
        );

        return $this;

    }

    /**
     * @return Collection<int,string>
     */
    public function getClusters(): Collection
    {
        return $this->clusters->map(fn ($cluster) => Str::after($cluster, 'cluster/'));
    }

    public function selectCluster(string $clusterName): static
    {
        $this->selectedCluster = $clusterName;

        return $this;
    }

    public function listServicesForCluster(string $clusterName = null): static
    {
        if ($clusterName) {
            $this->selectCluster($clusterName);
        }

        /** @phpstan-ignore-next-line */
        $this->services = collect(
            /** @phpstan-ignore-next-line */
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

    /**
     * @return Collection<int,string>
     */
    public function serviceNames(): Collection
    {
        return $this->filteredServices;
    }

    public function selectService(string $serviceName): static
    {
        $this->selectedService = $serviceName;

        return $this;
    }

    /**
     * @return $this
     */
    public function listTaskDefinitions(): static
    {
        /** @phpstan-ignore-next-line */
        $this->taskDefinitions = collect($this->client->listTasks(['serviceName' => $this->selectedService, 'cluster' => $this->selectedCluster])->get('taskArns'));

        return $this;
    }

    /**
     * @return Collection<int,string>
     */
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
