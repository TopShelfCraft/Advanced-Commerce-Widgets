<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\base\Widget;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\commerce\widgets\TotalOrders as CraftTotalOrdersWiget;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\TotalOrders as TotalOrdersStat;

class TotalOrders extends CraftTotalOrdersWiget
{

	/**
	 * @var string
	 */
	public $queryModifierKey;

	/**
	 * @var null|TotalOrdersStat
	 */
	private $_stat;

	public function init()
	{
		parent::init();
		$this->dateRange = !$this->dateRange ? TotalOrdersStat::DATE_RANGE_TODAY : $this->dateRange;

		$this->_stat = new TotalOrdersStat(
			$this->dateRange,
			DateTimeHelper::toDateTime($this->startDate),
			DateTimeHelper::toDateTime($this->endDate)
		);

		if ($this->queryModifierKey)
		{
			$callable = CommerceWidgets::getInstance()->queries->getOrderQueryModifierByKey($this->queryModifierKey);
			if ($callable)
			{
				$this->_stat->setQueryModifier($callable);
			}
		};

	}

	/**
	 * @inheritdoc
	 */
	public static function displayName(): string
	{
		return Craft::t('commerce', 'Total Orders - Improved');
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{
		if (!$this->showChart) {
			return '';
		}

		$stats = $this->_stat->get();
		$total = $stats['total'] ?? 0;
		$total = Craft::$app->getFormatter()->asInteger($total);

		return Craft::t('commerce', '{total} orders', ['total' => $total]);
	}

	public function getSubtitle()
	{
		if (!$this->showChart) {
			return '';
		}

		return $this->_stat->getDateRangeWording();
	}

	/**
	 * @inheritdoc
	 */
	public function getBodyHtml()
	{
		$showChart = $this->showChart;
		$stats = $this->_stat->get();
		$number = $stats['total'] ?? 0;
		$chart = $stats['chart'] ?? [];

		$labels = ArrayHelper::getColumn($chart, 'datekey', false);
		$data = ArrayHelper::getColumn($chart, 'total', false);

		$timeFrame = $this->_stat->getDateRangeWording();
		$number = Craft::$app->getFormatter()->asInteger($number);

		$id = 'total-orders' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		$view = Craft::$app->getView();
		$view->registerAssetBundle(StatWidgetsAsset::class);

		return $view->renderTemplate('commerce/_components/widgets/orders/total/body', compact(
			'namespaceId',
			'number',
			'timeFrame',
			'labels',
			'data',
			'showChart'
		));
	}

	/**
	 * @inheritDoc
	 */
	public static function maxColspan()
	{
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function getSettingsHtml(): string
	{

		$id = 'total-orders' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate('advanced-commerce-widgets/TotalOrders/settings', [
			'id' => $id,
			'namespaceId' => $namespaceId,
			'widget' => $this,
		]);

	}
}
