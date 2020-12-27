<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\base\Widget;
use craft\commerce\stats\TopProducts as TopProductsStat;
use craft\commerce\widgets\TopProducts as CraftTopProductsWidget;
use craft\helpers\DateTimeHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;

// TODO: extend base Widget instead.
class TopProducts extends CraftTopProductsWidget
{

	/*
	 * Instance
	 * ----------------------------------------------------------------------------------------------
	 */

	/**
	 * @inheritDoc
	 */
	public function init()
	{

		parent::init();

//		$this->_typeOptions = [
//			'qty' =>  Craft::t('commerce', 'Qty'),
//			'revenue' => Craft::t('commerce', 'Revenue'),
//		];

//		$this->dateRange = !$this->dateRange ? TopProductsStat::DATE_RANGE_TODAY : $this->dateRange;
//
//		$this->_stat = new TopProductsStat(
//			$this->dateRange,
//			$this->type,
//			DateTimeHelper::toDateTime($this->startDate),
//			DateTimeHelper::toDateTime($this->endDate)
//		);

	}

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{

		// TODO: nah.
		return parent::getTitle();

		if ($this->title)
		{
			return $this->title;
		}

		switch ($this->type) {
			case 'sales':
			{
				return CommerceWidgets::t('Top Products by Sales');
			}
			case 'revenue':
			{
				return CommerceWidgets::t('Top Products by Total Revenue');
			}
			case 'qty':
			{
				return Craft::t('commerce', 'Top Products by Qty Sold');
			}
			default:
			{
				return Craft::t('commerce', 'Top Products');
			}
		}

	}

	/**
	 * @inheritDoc
	 */
	public function getSubtitle()
	{
		// TODO: nah.
		return parent::getSubtitle();

		return $this->_stat->getDateRangeWording();
	}


	/*
	 * Static
	 * ----------------------------------------------------------------------------------------------
	 */

	/**
	 * @inheritdoc
	 */
	public static function displayName(): string
	{
		// TODO: Translate
		return Craft::t('commerce', 'Top Products') . " - Improved";
	}

	/**
	 * @inheritdoc
	 */
	public static function isSelectable(): bool
	{
		return CraftTopProductsWidget::isSelectable();
	}

	/**
	 * @inheritdoc
	 */
	public static function icon(): string
	{
		return CraftTopProductsWidget::icon();
	}

}
