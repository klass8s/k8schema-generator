<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Kubernetes;

use Klass8s\K8SchemaGenerator\App\Github\FileTreeService;

final readonly class JsonSchemaVersionsService
{
    private const GITHUB_USERNAME = 'yannh';
    private const GITHUB_REPO = 'kubernetes-json-schema';

    public function __construct(
        private FileTreeService $treeService,
        private VersionsService $versionsService,
    ) {
    }

    public function latestVersionsMap(int $numberOfVersions): array
    {
        $latestMinorVersions = $this->versionsService->latestMinorReleases($numberOfVersions);
        $topLevelItems = $this->treeService->topLevelItems(
            self::GITHUB_USERNAME,
            self::GITHUB_REPO
        );

        $dirs = array_filter(
            $topLevelItems['tree'],
            fn (array $item): bool => 'tree' === $item['type'],
        );
        $dirNames = array_column($dirs, 'path');
        $stableVersionPattern = '/v?\d+\.\d+\.\d+$/';
        $dirNames = array_filter(
            $dirNames,
            fn (string $dirname): bool => 0 !== preg_match($stableVersionPattern, $dirname),
        );

        $groupedByMinorRelease = [];
        $pattern = sprintf('/(%s)\.\d+$/', \implode('|', $latestMinorVersions));
        foreach ($dirNames as $dirname) {
            if (0 === preg_match($pattern, $dirname, $matches)) {
                continue;
            }
            $groupedByMinorRelease[$matches[1]] ??= [];
            $groupedByMinorRelease[$matches[1]][] = $dirname;
        }

        $versionsMap = [];
        foreach ($groupedByMinorRelease as $minorRelease => $dirNames) {
            usort($dirNames, 'version_compare');
            $latestJsonSchemaVersion = $dirNames[\count($dirNames) - 1];
            $versionsMap[$minorRelease] = $latestJsonSchemaVersion;
        }

        return $versionsMap;
    }
}
