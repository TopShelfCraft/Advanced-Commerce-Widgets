<?php
namespace topshelfcraft\commercewidgets\web\cp;

use craft\events\RegisterComponentTypesEvent;
use topshelfcraft\commercewidgets\web\widgets\TopProducts;
use topshelfcraft\commercewidgets\web\widgets\TotalOrders;

class CpCustomizations
{

	public function handleRegisterWidgetTypes(RegisterComponentTypesEvent $event): void
	{
		$event->types[] = TopProducts::class;
		$event->types[] = TotalOrders::class;
	}

}
