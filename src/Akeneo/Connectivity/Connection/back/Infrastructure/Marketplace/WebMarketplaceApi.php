<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Infrastructure\Marketplace;

use Akeneo\Platform\Bundle\FeatureFlagBundle\FeatureFlag;
use GuzzleHttp\ClientInterface;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class WebMarketplaceApi implements WebMarketplaceApiInterface
{
    private ClientInterface $client;
    private WebMarketplaceAliasesInterface $webMarketplaceAliases;
    private string $fixturePath;
    private FeatureFlag $fakeAppsFeatureFlag;

    public function __construct(
        ClientInterface $client,
        WebMarketplaceAliasesInterface $webMarketplaceAliases,
        FeatureFlag $fakeAppsFeatureFlag
    ) {
        $this->client = $client;
        $this->webMarketplaceAliases = $webMarketplaceAliases;
        $this->fakeAppsFeatureFlag = $fakeAppsFeatureFlag;
    }

    public function getExtensions(int $offset = 0, int $limit = 10): array
    {
        $edition = $this->webMarketplaceAliases->getEdition();
        $version = $this->webMarketplaceAliases->getVersion();

        $response = $this->client->request('GET', '/api/1.0/extensions', [
            'query' => [
                'extension_type' => 'connector',
                'edition' => $edition,
                'version' => $version,
                'offset' => $offset,
                'limit' => $limit,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getApps(int $offset = 0, int $limit = 10): array
    {
        if ($this->fakeAppsFeatureFlag->isEnabled()) {
            return json_decode(file_get_contents($this->fixturePath . 'marketplace-data-apps.json'), true);
        }

        $edition = $this->webMarketplaceAliases->getEdition();
        $version = $this->webMarketplaceAliases->getVersion();

        $response = $this->client->request('GET', '/api/1.0/extensions', [
            'query' => [
                'extension_type' => 'app',
                'edition' => $edition,
                'version' => $version,
                'offset' => $offset,
                'limit' => $limit,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getApp(string $id): ?array
    {
        $offset = 0;

        do {
            $result = $this->getApps($offset, 100);
            $offset += $result['limit'];

            foreach ($result['items'] as $item) {
                if ($id === $item['id']) {
                    return $item;
                }
            }
        } while (count($result['items']) > 0);

        return null;
    }

    public function setFixturePath(string $fixturePath)
    {
        $this->fixturePath = $fixturePath;
    }
}
