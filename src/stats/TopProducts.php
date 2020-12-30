<?php
namespace topshelfcraft\commercewidgets\stats;

use Craft;
use craft\commerce\db\Table;
use craft\commerce\Plugin as Commerce;
use craft\db\Table as CraftTable;
use yii\db\Expression;

class TopProducts extends BaseStat
{

	/**
	 * @inheritdoc
	 */
	protected $_handle = 'advancedCommerceWidgets_topProducts';

	/**
	 * @var string Type, either 'qty' or 'revenue'.
	 */
	public $type = 'qty';

	/**
	 * @var int
	 */
	public $limit = 5;

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$primarySite = Craft::$app->getSites()->getPrimarySite();
		$selectTotalQty = new Expression('SUM([[li.qty]]) as qty');
		$orderByQty = new Expression('SUM([[li.qty]]) DESC');
		$selectTotalRevenue = new Expression('SUM([[li.total]]) as revenue');
		$orderByRevenue = new Expression('SUM([[li.total]]) DESC');

		$topProducts = $this->_createStatQuery()
			->select([
				'[[v.productId]] as id',
				'[[content.title]]',
				$selectTotalQty,
				$selectTotalRevenue
			])
			->leftJoin(Table::LINEITEMS . ' li', '[[li.orderId]] = [[orders.id]]')
			->leftJoin(Table::PURCHASABLES . ' p', '[[p.id]] = [[li.purchasableId]]')
			->leftJoin(Table::VARIANTS . ' v', '[[v.id]] = [[p.id]]')
			->leftJoin(CraftTable::CONTENT . ' content', [
				'and',
				'[[content.elementId]] = [[v.productId]]',
				['content.siteId' => $primarySite->id],
			])
			->groupBy('[[v.productId]], [[content.title]]')
			->orderBy($this->type == 'revenue' ? $orderByRevenue : $orderByQty)
			->andWhere(['not', ['[[v.productId]]' => null]])
			->limit($this->limit);

		return $topProducts->all();
	}

	/**
	 * @inheritDoc
	 */
	public function getHandle(): string
	{
		return parent::getHandle() . $this->type;
	}

	/**
	 * @inheritDoc
	 */
	public function prepareData($data)
	{
		if (!empty($data)) {
			foreach ($data as &$row) {
				if ($row['id']) {
					$row['product'] = Commerce::getInstance()->getProducts()->getProductById($row['id']);
				}
			}
		}
		return $data;
	}

}
