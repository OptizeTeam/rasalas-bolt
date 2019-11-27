<?php

namespace Bolt\Extension\Optize\Rasalas;

use Bolt\Asset\File\JavaScript;
use Bolt\Controller\Zone;
use Bolt\Extension\Optize\Rasalas\Controller\RasalasBackendController;
use Bolt\Extension\SimpleExtension;
use Bolt\Menu\MenuEntry;

/**
 * Rasalas extension class.
 *
 * @author Optize sp. z o.o. <hello@optize.pl>
 */
class RasalasExtension extends SimpleExtension
{
	/**
	 * @return string
	 */
	public function getDisplayName()
    {
        return 'Tap4Call – easy call back module';
	}

	/**
     * {@inheritdoc}
     */
    protected function registerAssets()
    {
        $rasalas_script_id = $this->getConfig()['id'];

        $script = JavaScript::create()
			->setFileName('https://panel.tap4call.com/widget/tap4call.js')
			->setAttributes(array(
				'id' => $rasalas_script_id
			))
			->setLate(true)
			->setPriority(5)
			->setZone(Zone::FRONTEND)
        ;

        return [
            $script,
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	protected function registerTwigPaths()
	{
		return [
			'templates' => ['namespace' => 'TAP4CALL'],
		];
    }

	/**
	 * @return array
	 */
	protected function registerMenuEntries()
	{
		$RasalasMenu = new MenuEntry('rasalas-menu', 'rasalas');
		$RasalasMenu
			->setLabel('TAP4CALL - Ustawienia')
			->setIcon('fa:cogs')
			->setPermission('settings');

		return [
			$RasalasMenu
		];
	}

	/**
	 * @return array
	 */
	protected function registerBackendControllers()
	{
		return [
			'/extensions/rasalas' => new RasalasBackendController($this->getConfig())
		];
	}
}
