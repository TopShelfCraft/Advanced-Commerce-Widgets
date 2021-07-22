<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\TotalOrders as TotalOrdersStat;

class TotalOrders extends \craft\commerce\widgets\TotalOrders
{

	use AdvancedWidgetTrait;

	/**
	 * @var null|TotalOrdersStat
	 */
	private $_stat;

	public function init()
	{

		parent::init();
		$this->dateRange = $this->dateRange ?: TotalOrdersStat::DATE_RANGE_TODAY;

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
		return Craft::t('commerce', 'Total Orders') . " - Advanced";
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{
		return $this->showChart ? $this->_getTitle() : '';
	}

	/**
	 * @inheritdoc
	 */
	public function getSubtitle()
	{
		return $this->showChart ? $this->_getSubtitle() : '';
	}

	/**
	 * @inheritdoc
	 */
	public function getBodyHtml()
	{

		$view = Craft::$app->getView();
		$view->registerAssetBundle(StatWidgetsAsset::class);

		$stats = $this->_stat->get();

		if (!$this->showChart)
		{

			$number = $stats['total'] ?? 0;
			$number = Craft::$app->getFormatter()->asInteger($number);

			return $view->renderTemplate(
				'advanced-commerce-widgets/_statWidget/simple',
				[
					'value' => $number,
					'title' => $this->_getTitle(),
					'subtitle' => $this->_getSubtitle(),
				]
			);

		}

		$chart = $stats['chart'] ?? [];
		$labels = ArrayHelper::getColumn($chart, 'datekey', false);
		$data = ArrayHelper::getColumn($chart, 'total', false);

		$id = 'total-orders' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		$showChart = true;

		return $view->renderTemplate(
			'commerce/_components/widgets/orders/total/body',
			compact(
				'namespaceId',
				'labels',
				'data',
				'showChart'
			)
		);

	}

	/**
	 * @inheritdoc
	 */
	public function getSettingsHtml(): string
	{

		$id = 'total-orders' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate(
			'advanced-commerce-widgets/TotalOrders/settings',
			[
				'id' => $id,
				'namespaceId' => $namespaceId,
				'widget' => $this,
			]
		);

	}

	private function _getTitle(): string
	{

		$total = $this->_getValue();

		$defaultTitle = Craft::t('commerce', '{total} orders', ['total' => $total]);
		return CommerceWidgets::t(
			$this->customTitle,
			[
				'title' => $defaultTitle,
				'value' => $total,
			]
		);

	}

	private function _getSubtitle(): string
	{
		$defaultSubtitle = $this->_stat->getDateRangeWording();
		return CommerceWidgets::t(
			$this->customSubtitle,
			[
				'subtitle' => $defaultSubtitle,
				'value' => $this->_getValue(),
			]
		);
	}

	private function _getValue()
	{
		$stats = $this->_stat->get();
		$total = $stats['total'] ?? 0;
		return Craft::$app->getFormatter()->asInteger($total);
	}

}
