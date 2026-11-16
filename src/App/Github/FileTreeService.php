<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Github;

use Github\Client;

final readonly class FileTreeService
{
    private const string BRANCH_MASTER = 'master';

    public function __construct(
        private Client $githubClient,
        private CommitsService $commitsService,
    ) {
    }

    public function topLevelItems(string $githubUsername, string $repoName): array
    {
        $latestCommit = $this->commitsService->latestCommitInBranch(
            $githubUsername,
            $repoName,
            self::BRANCH_MASTER
        );

        $sha = $latestCommit['sha'];

        return $this->githubClient
            ->api('gitData')
            ->trees()
            ->show($githubUsername, $repoName, $sha);
    }
}
