<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiSatScraper\Tests\Integration;

use PhpCfdi\CfdiSatScraper\Filters\Options\DownloadTypesOption;
use PhpCfdi\CfdiSatScraper\Query;

class RetrieveByDateTimeTest extends IntegrationTestCase
{
    /**
     * @param DownloadTypesOption $downloadType
     * @dataProvider providerEmitidosRecibidos
     */
    public function testRetrieveByDateTime(DownloadTypesOption $downloadType): void
    {
        $repository = $this->getRepository()->filterByType($downloadType);
        $typeText = $this->getDownloadTypeText($downloadType);
        if ($repository->count() < 2) {
            $this->markTestSkipped("Unable to test because there are less than 2 records in $typeText");
        }

        $since = $repository->getSinceDate();
        $until = $repository->getUntilDate();
        if ($since > $until) {
            $this->markTestSkipped("Unable to test because in $typeText the since and until date are equal");
        }

        $scraper = $this->getSatScraper();
        $query = (new Query($since, $until))->setDownloadType($downloadType);
        $list = $scraper->downloadByDateTime($query);

        $this->assertRepositoryEqualsMetadataList($repository, $list);
    }
}
