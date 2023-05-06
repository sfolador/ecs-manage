<?php

namespace Sfolador\EcsManage;

use Illuminate\Support\Collection;

interface EcsManageInterface
{
    public function getEnvironments(): mixed;

    public function listClusters(): static;

    /**
     * @return Collection<int,string>
     */
    public function getClusters(): Collection;

    public function selectCluster(string $clusterName): static;

    public function listServicesForCluster(string $clusterName = null): static;

    public function filterServices(string $filterWord): static;

    /**
     * @return Collection<int,string>
     */
    public function serviceNames(): Collection;

    public function selectService(string $serviceName): static;

    public function listTaskDefinitions(): static;

    /**
     * @return Collection<int,string>
     */
    public function taskDefinitionNames(): Collection;

    public function selectTask(string $taskName): static;

    public function createCommand(): string;
}
