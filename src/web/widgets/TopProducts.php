<?php
namespace topshelfcraft\commercewidgets\web\widgets;

use Craft;
use craft\commerce\web\assets\statwidgets\StatWidgetsAsset;
use craft\commerce\widgets\TopProducts as CraftTopProductsWidget;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use craft\web\assets\admintable\AdminTableAsset;
use topshelfcraft\commercewidgets\CommerceWidgets;
use topshelfcraft\commercewidgets\stats\TopProducts as TopProductsStat;

class TopProducts extends CraftTopProductsWidget
{

	use AdvancedWidgetTrait;

	/**
	 * @var string
	 */
	public $type = 'qty';

	/**
	 * @var array
	 */
	private $_typeOptions;

	/**
	 * @var null|TopProductsStat
	 */
	private $_stat;

	/**
	 * @inheritDoc
	 */
	public function init()
	{

		parent::init();

		$this->_typeOptions = [
			'qty' =>  Craft::t('commerce', 'Qty'),
			'revenue' => Craft::t('commerce', 'Revenue'),
		];

		$this->dateRange = $this->dateRange ?: TopProductsStat::DATE_RANGE_TODAY;

		$this->_stat = new TopProductsStat(
			$this->dateRange,
			DateTimeHelper::toDateTime($this->startDate),
			DateTimeHelper::toDateTime($this->endDate)
		);

		$this->_stat->type = $this->type;

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

		$id = 'total-orders' . StringHelper::randomString();
		$namespaceId = Craft::$app->getView()->namespaceInputId($id);

		return Craft::$app->getView()->renderTemplate(
			'advanced-commerce-widgets/TopProducts/settings',
			[
				'id' => $id,
				'namespaceId' => $namespaceId,
				'widget' => $this,
				'typeOptions' => $this->_typeOptions,
			]
		);

	}

}
