<?php

declare(strict_types=1);

namespace Pim\Bundle\CatalogVolumeMonitoringBundle\tests\Acceptance\Context;

use Behat\Behat\Context\Context;
use Pim\Bundle\CatalogVolumeMonitoringBundle\tests\Acceptance\Persistence\Query\InMemory\InMemoryCountQuery;
use Pim\Component\CatalogVolumeMonitoring\Volume\Normalizer;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /** @var Normalizer\Volumes */
    private $volumesNormalizer;

    /** @var InMemoryCountQuery */
    private $inMemoryQuery;

    /** @var array */
    private $volumes = [];

    /**
     * @param Normalizer\Volumes $volumesNormalizer
     * @param InMemoryCountQuery $inMemoryQuery
     */
    public function __construct(Normalizer\Volumes $volumesNormalizer, InMemoryCountQuery $inMemoryQuery)
    {
        $this->volumesNormalizer = $volumesNormalizer;
        $this->inMemoryQuery = $inMemoryQuery;
    }

    /**
     * @Given a catalog with :numberOfProducts products
     *
     * @param int $numberOfProducts
     */
    public function aCatalogWithProducts(int $numberOfProducts): void
    {
        $this->inMemoryQuery->setVolume($numberOfProducts);
    }

    /**
     * @Given the limit of the number of products is set to :limit
     *
     * @param int $limit
     */
    public function theLimitOfTheNumberOfProductsIsSetTo(int $limit): void
    {
        $this->inMemoryQuery->setLimit($limit);
    }

    /**
     * @When the administrator user asks for the report to monitor the number of products
     */
    public function theAdministratorUserAsksForTheReportToMonitorTheNumberOfProducts(): void
    {
        $this->volumes = $this->volumesNormalizer->volumes();
    }

    /**
     * @Then the report returns that the number of products is :numberOfProducts
     *
     * @param int $numberOfProducts
     */
    public function theReportReturnsThatTheNumberOfProductsIs(int $numberOfProducts): void
    {
        Assert::eq($numberOfProducts, $this->volumes['products']['value']);
    }

    /**
     * @Then the report warns the users that the number of products is high
     */
    public function theReportWarnsTheUsersThatTheNumberIsHigh(): void
    {
        Assert::true($this->volumes['products']['has_warning']);
    }

    /**
     * @Then the report does not warn the users that the number of products is high
     */
    public function theReportDoesNotWarnTheUsersThatTheNumberIsHigh(): void
    {
        Assert::false($this->volumes['products']['has_warning']);
    }
}
