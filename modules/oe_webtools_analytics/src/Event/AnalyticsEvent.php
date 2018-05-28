<?php

declare(strict_types = 1);

/**
 * Webtools AnalyticsEvent Event.
 *
 * @see https://webgate.ec.europa.eu/fpfis/wikis/pages/viewpage.action?spaceKey=webtools&title=Piwik
 */

namespace Drupal\oe_webtools_analytics\Event;

use JsonSerializable;
use Symfony\Component\EventDispatcher\Event;
use Drupal\oe_webtools_analytics\AnalyticsEventInterface;
use Drupal\oe_webtools_analytics\Entity\SearchParametersInterface;

/**
 * Class WebtoolsImportDataEvent.
 *
 * @package Drupal\oe_webtools_analytics\Event
 */
class AnalyticsEvent extends Event implements JsonSerializable, AnalyticsEventInterface {
  /**
   * This event allows you to set the Analytics variable.
   *
   * @Event Drupal\oe_webtools_analytics\Event\WebtoolsImportDataEvent
   */
  public const NAME = 'webtools_analytics.data_collection';

  /**
   * The site ID (mandatory).
   *
   * @var string
   */
  private $siteId;

  /**
   * A specific section or a subwebsite of main site.
   *
   * @var string
   */
  private $siteSection;

  /**
   * Allows you to define the root path of your website.
   *
   * @var string[]
   */
  private $sitePath;

  /**
   * Set this variable to true on your 404 page.
   *
   * @var bool
   */
  private $is404Page;

  /**
   * Set this variable to true on your 403 page.
   *
   * @var bool
   */
  private $is403Page;

  /**
   * Allows to override or set the language of the current page (optional).
   *
   * @var string
   */
  private $langCode;

  /**
   * Allows to switch between these instances (optional).
   *
   *   - ec.europa.eu
   *   - europa.eu
   *   - testing.
   *
   * @var string
   */
  private $instance;

  /**
   * The Search result in json format.
   *
   * @var \Drupal\oe_webtools_analytics\Entity\SearchParametersInterface
   */
  private $search;

  /**
   * The analytic parameter.
   *
   * @var string
   *   A string which by default it sets to "piwik".
   */
  private $utility;

  /**
   * AnalyticsEvent constructor.
   */
  public function __construct() {
    // This is to prevent issues when serializing the object.
    // Those settings are temporary until a UI exists to set them.
    $this->setUtility();
    $this->setSiteSection();
    $this->setLangCode();
    $this->setInstance();
    $this->setIs404Page();
    $this->setIs403Page();
    $this->setSiteId();
  }

  /**
   * {@inheritdoc}
   */
  public function setSearchParameters(SearchParametersInterface $searchParameters): void {
    $this->search = $searchParameters;
  }

  /**
   * {@inheritdoc}
   */
  public function setSiteId(string $siteId = 'n/a'): void {
    $this->siteId = $siteId;
  }

  /**
   * {@inheritdoc}
   */
  public function setSitePath(array $sitePath): void {
    $this->sitePath = $sitePath;
  }

  /**
   * {@inheritdoc}
   */
  public function setSiteSection(string $siteSection = ''): void {
    $this->siteSection = $siteSection;
  }

  /**
   * {@inheritdoc}
   */
  public function setIs404Page(bool $is404Page = FALSE): void {
    $this->is404Page = $is404Page;
  }

  /**
   * {@inheritdoc}
   */
  public function setIs403Page(bool $is403Page = FALSE): void {
    $this->is403Page = $is403Page;
  }

  /**
   * {@inheritdoc}
   */
  public function setLangCode(string $langCode = ''): void {
    $this->langCode = $langCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setInstance(string $instance = ''): void {
    $this->instance = $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function setUtility(string $utility = 'piwik'): void {
    $this->utility = $utility;
  }

  /**
   * {@inheritdoc}
   */
  public function getSiteId(): string {
    return $this->siteId;
  }

  /**
   * {@inheritdoc}
   */
  public function getSiteSection(): string {
    return $this->siteSection;
  }

  /**
   * {@inheritdoc}
   */
  public function getSitePath(): array {
    return $this->sitePath;
  }

  /**
   * {@inheritdoc}
   */
  public function is404Page(): bool {
    return $this->is404Page;
  }

  /**
   * {@inheritdoc}
   */
  public function is403Page(): bool {
    return $this->is403Page;
  }

  /**
   * {@inheritdoc}
   */
  public function getLangCode(): string {
    return $this->langCode;
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(): string {
    return $this->instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearch(): SearchParametersInterface {
    return $this->search;
  }

  /**
   * {@inheritdoc}
   */
  public function getUtility(): string {
    return $this->utility;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize() {
    $data = [
      self::UTILITY => $this->getUtility(),
      self::SITE_ID => $this->getSiteId(),
      self::SITE_PATH => $this->getSitePath(),
      self::SITE_SECTION => $this->getSiteSection(),
      self::IS404 => $this->is404Page(),
      self::IS403 => $this->is403Page(),
      self::LANG => $this->getLangCode(),
      self::INSTANCE => $this->getInstance(),
    ];

    if ($this->search->isSetKeyword()) {
      $data[self::SEARCH] = $this->search->jsonSerialize();
    }

    return array_filter($data);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return json_encode($this);
  }

  /**
   * {@inheritdoc}
   */
  public function isValid(): bool {
    // SiteId is required.
    return NULL !== $this->getSiteId();
  }

}
