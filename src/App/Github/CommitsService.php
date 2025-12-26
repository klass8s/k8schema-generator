<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Github;

use Github\Client;

final readonly class CommitsService
{
    public function __construct(private Client $githubClient)
    {
    }

    public function all(string $githubUsername, string $repoName, string $branchName)
    {
        return $this->githubClient
            ->api('repo')
            ->commits()
            ->all($githubUsername, $repoName, ['sha' => $branchName]);
    }

    public function latestCommitInBranch(string $githubUsername, string $repoName, string $branchName)
    {
        return $this->all($githubUsername, $repoName, $branchName)[0];
    }
}
