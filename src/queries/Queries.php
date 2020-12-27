<?php
namespace topshelfcraft\commercewidgets\queries;

use craft\commerce\elements\db\OrderQuery;
use craft\commerce\elements\Order;
use craft\commerce\Plugin as Commerce;
use craft\db\Query;
use craft\helpers\StringHelper;
use topshelfcraft\commercewidgets\CommerceWidgets;

class Queries
{

	public function getOrderQueryModifierSelectOptions(): array
	{

		// TODO: Translate
		$options = [
			':all' => 'All Orders',
		];

		foreach (Commerce::getInstance()->orderStatuses->getAllOrderStatuses() as $status)
		{
			$options['status:' . $status->id] = $status->name;
		}

		$userDefinedQueries = CommerceWidgets::getInstance()->getSettings()->orderQueryModifiers;

		foreach (array_keys($userDefinedQueries) as $key)
		{
			$options[$key] = $key;
		}

		return $options;

	}

	public function getOrderQueryModifierByKey(string $key): ?callable
	{

		if ($key === ':all')
		{
			return function(Query $query) {
				return $query;
			};
		}

		if (StringHelper::startsWith($key, 'status:'))
		{
			$orderStatusId = (int) StringHelper::afterFirst($key, 'status:');
			return function(Query $query) use ($orderStatusId) {
				return $query->andWhere(['orderStatusId' => $orderStatusId]);
			};
		}

		$userDefinedModifiers = CommerceWidgets::getInstance()->getSettings()->orderQueryModifiers;

		if (isset($userDefinedModifiers[$key]))
		{
			return $userDefinedModifiers[$key];
		}

		return null;

		// TODO: Better exception.
		throw new \Exception("User defined query modifier \"{$key}\" not specified in config.");

	}

}
