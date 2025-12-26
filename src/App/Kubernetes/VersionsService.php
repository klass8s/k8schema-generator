<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Kubernetes;

use Tequila\K8S\SchemaGenerator\App\Github\ReleasesService;

final readonly class VersionsService
{
    public const string GITHUB_USERNAME = 'kubernetes';
    public const string GITHUB_REPO = 'kubernetes';

    public function __construct(private ReleasesService $releasesService)
    {
    }

    /**
     * @param int $numberOfReleases
     *
     * @return string[]|array
     */
    public function latestMinorReleases(int $numberOfReleases): array
    {
        return $this->releasesService->latestMinorReleasesNames(
            self::GITHUB_USERNAME,
            self::GITHUB_REPO,
            $numberOfReleases
        );
    }
}
