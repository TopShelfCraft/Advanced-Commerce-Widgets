<?php
namespace topshelfcraft\commercewidgets\web\cp;

use craft\events\RegisterComponentTypesEvent;
use topshelfcraft\commercewidgets\web\widgets\AverageOrderTotal;
use topshelfcraft\commercewidgets\web\widgets\TopProducts;
use topshelfcraft\commercewidgets\web\widgets\TotalOrders;
use topshelfcraft\commercewidgets\web\widgets\TotalRevenue;

class CpCustomizations
{

	public function handleRegisterWidgetTypes(RegisterComponentTypesEvent $event): void
	{
		$event->types[] = AverageOrderTotal::class;
		$event->types[] = TopProducts::class;
		$event->types[] = TotalOrders::class;
		$event->types[] = TotalRevenue::class;
	}

}
