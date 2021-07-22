<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\commerce\helpers\Currency;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\TotalRevenue as TotalRevenueStat;

class TotalRevenue extends \craft\commerce\widgets\TotalRevenue
{

	use AdvancedWidgetTrait;

	/**
	 * @var null|TotalRevenueStat
	 */
	private $_stat;

	public function init()
	{

		parent::init();
		$this->dateRange = $this->dateRange ?: TotalRevenueStat::DATE_RANGE_TODAY;

		$this->_stat = new TotalRevenueStat(
			$this->dateRange,
			DateTimeHelper::toDateTime($this->startDate),
			DateTimeHelper::toDateTime($this->endDate)
		);

		if ($this->queryModifierKey)
		{
			$callable = CommerceWidgets::getInstance()->queries->getOrderQueryModifierByKey($this->queryModifierKey);
			if ($callable)
			{
				$this->_stat->setCacheNamespace($this->queryModifierKey);
				$this->_stat->setQueryModifier($callable);
			}
		};

	}

	/**
	 * @inheritdoc
	 */
	public static function displayName(): string
	{
		return Craft::t('commerce', 'Total Revenue') . " - Advanced";
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{

		$formattedTotal = $this->_getValue();
		$defaultTitle = Craft::t('commerce', '{total} in total revenue', ['total' => $formattedTotal]);

		return CommerceWidgets::t(
			$this->customTitle,
			[
				'title' => $defaultTitle,
				'value' => $formattedTotal,
			]
		);

	}

	/**
	 * @inheritdoc
	 */
	public function getSubtitle()
	{
		$defaultSubtitle = $this->_stat->getDateRangeWording();
		return CommerceWidgets::t(
			$this->customSubtitle,
			[
				'subtitle' => $defaultSubtitle,
				'value' => $this->_getValue()
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	public function getBodyHtml()
	{

		$stats = $this->_stat->get();
		$timeFrame = $this->_stat->getDateRangeWording();
		$chartInterval = $this->_stat->getDateRangeInterval();

		$view = Craft::$app->getView();
		$view->registerAssetBundle(StatWidgetsAsset::class);

		$id = 'total-revenue' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		if (empty($stats)) {
			// TODO no stats available message
			return '';
		}

		$labels = ArrayHelper::getColumn($stats, 'datekey', false);
		if ($this->_stat->getDateRangeInterval() == 'month') {
			$labels = array_map(static function($label) {
				list($year, $month) = explode('-', $label);
				$month = $month < 10 ? '0' . $month : $month;
				return implode('-', [$year, $month, '01']);
			}, $labels);
		} else if ($this->_stat->getDateRangeInterval() == 'week') {
			$labels = array_map(static function($label) {
				$year = substr($label, 0, 4);
				$week = substr($label, -2);
				return $year . 'W' . $week;
			}, $labels);
		}

		$revenue = ArrayHelper::getColumn($stats, 'revenue', false);
		$orderCount = ArrayHelper::getColumn($stats, 'count', false);
		$widget = $this;

		return $view->renderTemplate('commerce/_components/widgets/orders/revenue/body',
			compact(
				'widget',
				'stats',
				'timeFrame',
				'namespaceId',
				'labels',
				'revenue',
				'orderCount',
				'chartInterval'
			)
		);
	}

	/**
	 * @inheritdoc
	 */
	public function getSettingsHtml(): string
	{

		$id = 'total-revenue' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate(
			'advanced-commerce-widgets/TotalRevenue/settings',
			[
				'id' => $id,
				'namespaceId' => $namespaceId,
				'widget' => $this,
			]
		);

	}

	private function _getValue()
	{
		$stats = $this->_stat->get();
		$revenue = ArrayHelper::getColumn($stats, 'revenue', false);
		$total = round(array_sum($revenue), 0, PHP_ROUND_HALF_DOWN);
		return Currency::formatAsCurrency($total, null, false, true, true);
	}

}
