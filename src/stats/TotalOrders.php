<?php
namespace topshelfcraft\commercewidgets\stats;

use yii\db\Expression;

class TotalOrders extends BaseStat
{

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_totalOrders';

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$query = $this->_createStatQuery();
		$query->select([new Expression('COUNT([[orders.id]]) as total')]);

		$chartData = $this->_createChartQuery([
			new Expression('COUNT([[orders.id]]) as total'),
		], [
			'total' => 0,
		]);

		return [
			'total' => $query->scalar(),
			'chart' => $chartData,
		];
	}

}
