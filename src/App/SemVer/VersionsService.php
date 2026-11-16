<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\SemVer;

final readonly class VersionsService
{
    /**
     * Returns the latest most stable version, or null, if there are no versions
     *
     * @param Version[] $versions
     */
    public function latestMostStable(array $versions): Version
    {
        $v = $versions;

        return ($this->latestStable($v) ?? $this->latestBeta($v)) ?? $this->latest($v);
    }

    /**
     * Returns the latest stable version, or null, if there are no stable versions
     *
     * @param Version[] $versions
     */
    public function latestStable(array $versions): ?Version
    {
        foreach ($this->sortDescending($versions) as $version) {
            if (!$version->stable()) {
                continue;
            }

            return $version;
        }

        return null;
    }

    /**
     * @param Version[] $versions
     */
    public function latestBeta(array $versions): ?Version
    {
        return array_find(
            $this->sortDescending($versions),
            fn (Version $version) => $version->beta(),
        );
    }

    /**
     * @param Version[] $versions
     */
    public function latest(array $versions): Version
    {
        return $this->sortDescending($versions)[0];
    }

    /**
     * @return Version[]
     */
    private function sortDescending(array $versions): array
    {
        usort(
            $versions,
            fn (Version $lh, Version $rh) => version_compare($rh->string(), $lh->string())
        );

        return $versions;
    }
}
