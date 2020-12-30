<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\commerce\helpers\Currency;
use craft\commerce\Plugin as Commerce;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\commerce\widgets\AverageOrderTotal as CraftAverageOrderTotalWidget;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\AverageOrderTotal as AverageOrderTotalStat;

class AverageOrderTotal extends CraftAverageOrderTotalWidget
{

	use AdvancedWidgetTrait;

	/**
	 * @var null|AverageOrderTotalStat
	 */
	private $_stat;

	public function init()
	{

		parent::init();
		$this->dateRange = $this->dateRange ?: AverageOrderTotalStat::DATE_RANGE_TODAY;

		$this->_stat = new AverageOrderTotalStat(
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
		return Craft::t('commerce', 'Average Order Total') . " - Advanced";
	}

	/**
	 * @inheritdoc
	 */
	public function getBodyHtml()
	{

		$view = Craft::$app->getView();
		$view->registerAssetBundle(StatWidgetsAsset::class);

		return $view->renderTemplate(
			'advanced-commerce-widgets/_statWidget/simple',
			[
				'value' => $this->_getValue(),
				'title' => $this->_getTitle(),
				'subtitle' => $this->_getSubtitle(),
			]
		);

	}

	/**
	 * @inheritdoc
	 */
	public function getSettingsHtml(): string
	{

		$id = 'average-order-total' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate(
			'advanced-commerce-widgets/AverageOrderTotal/settings',
			[
				'id' => $id,
				'namespaceId' => $namespaceId,
				'widget' => $this,
			]
		);
	}

	private function _getTitle(): string
	{

		$defaultTitle = Craft::t('commerce', 'average order total');
		return CommerceWidgets::t(
			$this->customTitle,
			[
				'title' => $defaultTitle,
				'value' => $this->_getValue(),
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
		$number = $this->_stat->get() ?? 0;
		return Currency::formatAsCurrency($number);
	}

}