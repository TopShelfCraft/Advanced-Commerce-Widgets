<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use craft\web\assets\admintable\AdminTableAsset;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\TopProducts as TopProductsStat;

class TopProducts extends \craft\commerce\widgets\TopProducts
{

	use AdvancedWidgetTrait;

	/**
	 * @var array
	 */
	private $_revenueCheckboxOptions;

	/**
	 * @var null|TopProductsStat
	 */
	private $_stat;

	/**
	 * @var array
	 */
	private $_typeOptions;

	/**
	 * @inheritDoc
	 */
	public function init()
	{

		parent::init();

		$this->_typeOptions = [
			TopProductsStat::TYPE_QTY => Craft::t('commerce', 'Qty'),
			TopProductsStat::TYPE_REVENUE => Craft::t('commerce', 'Revenue'),
		];

		$this->_revenueCheckboxOptions = [
			[
				'value' => TopProductsStat::REVENUE_OPTION_DISCOUNT,
				'label' => Craft::t('commerce', 'Discount'),
				'checked' => in_array(TopProductsStat::REVENUE_OPTION_DISCOUNT, $this->revenueOptions, true),
				'instructions' => Craft::t('commerce', 'Include line item discounts.'),
			],
			[
				'value' => TopProductsStat::REVENUE_OPTION_TAX_INCLUDED,
				'label' => Craft::t('commerce', 'Tax (inc)'),
				'checked' => in_array(TopProductsStat::REVENUE_OPTION_TAX_INCLUDED, $this->revenueOptions, true),
				'instructions' => Craft::t('commerce', 'Include built-in line item tax.'),
			],
			[
				'value' => TopProductsStat::REVENUE_OPTION_TAX,
				'label' => Craft::t('commerce', 'Tax'),
				'checked' => in_array(TopProductsStat::REVENUE_OPTION_TAX, $this->revenueOptions, true),
				'instructions' => Craft::t('commerce', 'Include separate line item tax.'),
			],
			[
				'value' => TopProductsStat::REVENUE_OPTION_SHIPPING,
				'label' => Craft::t('commerce', 'Shipping'),
				'checked' => in_array(TopProductsStat::REVENUE_OPTION_SHIPPING, $this->revenueOptions, true),
				'instructions' => Craft::t('commerce', 'Include line item shipping costs.'),
			],
		];

		$this->dateRange = $this->dateRange ?: TopProductsStat::DATE_RANGE_TODAY;

		$this->_stat = new TopProductsStat(
			$this->dateRange,
			$this->type,
			DateTimeHelper::toDateTime($this->startDate, true),
			DateTimeHelper::toDateTime($this->endDate, true),
			$this->revenueOptions
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
		// TODO: Translate
		return Craft::t('commerce', 'Top Products') . " - Advanced";
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{

		switch ($this->type) {
			case 'revenue':
			{
				$defaultTitle = Craft::t('commerce', 'Top Products by Revenue');
				break;
			}
			case 'qty':
			default:
			{
				$defaultTitle = Craft::t('commerce', 'Top Products by Qty Sold');
				break;
			}
		}

		return CommerceWidgets::t(
			$this->customTitle,
			[
				'title' => $defaultTitle,
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
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	public function getBodyHtml()
	{

		$stats = $this->_stat->get();

		$view = Craft::$app->getView();
		$view->registerAssetBundle(StatWidgetsAsset::class);
		$view->registerAssetBundle(AdminTableAsset::class);

		return $view->renderTemplate(
			'commerce/_components/widgets/products/top/body',
			[
				'stats' => $stats,
				'type' => $this->type,
				'typeLabel' => $this->_typeOptions[$this->type] ?? '',
				'id' => 'top-products' . StringHelper::randomString(),
			]
		);

	}

	/**
	 * @inheritdoc
	 */
	public function getSettingsHtml(): string
	{

		$id = 'top-products' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate(
			'advanced-commerce-widgets/TopProducts/settings',
			[
				'id' => $id,
				'namespaceId' => $namespaceId,
				'widget' => $this,
				'typeOptions' => $this->_typeOptions,
				'revenueOptions' => $this->_revenueCheckboxOptions,
				'isRevenueOptionsEnabled' => $this->type === TopProductsStat::TYPE_REVENUE
			]
		);

	}

}
