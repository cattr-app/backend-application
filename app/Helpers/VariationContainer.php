<?php

namespace App\Helpers;

use App\Contracts\VariationProviderContract as VariationProvider;
use Illuminate\Support\Collection;

class VariationContainer
{
    private Collection $providers;

    public function __construct()
    {
        $this->providers = collect();
    }

    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(VariationProvider $provider): self
    {
        $this->providers->add($provider);

        return $this;
    }

    public function addProviders(array $providers): self
    {
        $this->providers = $this->providers->merge($providers);

        return $this;
    }

    public function removeByMask(int $mask): self
    {
        $this->providers = $this->providers->reject(
            static fn(VariationProvider $provider) => $provider->getMask() === $mask
        );

        return $this;
    }

    public function getByMask(int $mask): VariationProvider
    {
        return $this->providers->firstOrFail(static fn(VariationProvider $provider) => $provider->getMask() === $mask);
    }
}
