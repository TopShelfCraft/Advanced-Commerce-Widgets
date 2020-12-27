<?php
namespace topshelfcraft\commercewidgets;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\services\Dashboard;
use topshelfcraft\commercewidgets\config\Settings;
use topshelfcraft\commercewidgets\queries\Queries;
use topshelfcraft\commercewidgets\web\cp\CpCustomizations;
use topshelfcraft\ranger\Plugin;
use yii\base\Event;

/**
 * Module to encapsulate Commerce Widgets functionality.
 *
 * This class will be available throughout the system via:
 * `Craft::$app->getModule('commerce-widgets')`
 *
 * @see http://www.yiiframework.com/doc-2.0/guide-structure-modules.html
 *
 * @property CpCustomizations $cp
 * @property Queries $queries
 *
 * @method Settings getSettings()
 *
 * @todo AverageOrderTotal (Commerce)
 * @todo NewCustomers (Commerce)
 * @todo Orders (Commerce)
 * @todo RepeatCustomers (Commerce)
 * @todo TopCustomers (Commerce)
 * @todo TopProducts (Commerce)
 * @todo TopProductTypes (Commerce)
 * @todo TopPurchasables (Commerce)
 * @todo TotalOrders (Commerce)
 * @todo TotalOrdersByCountry (Commerce)
 * @todo TotalRevenue (Commerce)
 *
 * @todo CartAbandonment
 * @todo Goal
 * @todo ProductsRecent
 * @todo ProductsTop
 * @todo SubscriptionPlans
 * @todo TopCustomers
 * @todo TotalRevenueOrders
 */
class CommerceWidgets extends BasePlugin
{

	/*
     * Instance
     * ===========================================================================
     */

	/**
	 * @var bool
	 */
	public $hasCpSettings = false;

	/**
	 * @var bool
	 */
	public $hasCpSection = false;

	/**
	 * @var string
	 */
	public $schemaVersion = '0.0.0.0';

	public function __construct($id, $parent = null, array $config = [])
	{
		$config['components'] = [
			'cp' => CpCustomizations::class,
			'queries' => Queries::class,
		];
		parent::__construct($id, $parent, $config);
	}

	/**
	 * @inheritdoc
	 */
	public function init(): void
	{

		parent::init();
		Plugin::watch($this);

		Craft::setAlias('@topshelfcraft/commercewidgets', __DIR__);
		parent::init();

		$this->_registerEventHandlers();

	}

	/**
	 * @inheritdoc
	 */
	protected function createSettingsModel(): ?Settings
	{
		return new Settings();
	}

	private function _registerEventHandlers(): void
	{
		Event::on(
			Dashboard::class,
			Dashboard::EVENT_REGISTER_WIDGET_TYPES,
			[$this->cp, 'handleRegisterWidgetTypes']
		);
	}

	/*
     * Static
     * ===========================================================================
     */

	public static function t($message, $params = [], $language = null): string
	{
		return Craft::t(self::getInstance()->getHandle(), $message, $params, $language);
	}

}
