<?php

namespace Bolt\Extension\Optize\Rasalas\Controller;

use Bolt\Filesystem\Handler\YamlFile;
use Bolt\Filesystem\Exception\DumpException;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RasalasBackendController implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param Application $app
     *
     * @return mixed
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        /**
         * @var ControllerCollection
         */
        $c = $app['controllers_factory'];

        $c->get('/', [$this, 'getIndex'])->bind('rasalas');
        $c->post('/', [$this, 'postIndex']);

        return $c;
    }

    /**
     * @return Response
     */
    public function getIndex()
    {
        return $this->app['render']->render('@Rasalas/rasalas.twig', $this->config, []);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function postIndex(Request $request)
    {
        $session = $this->app['session'];

        if ($this->saveExtensionSettings($request)) {
            $session->getFlashBag()->add('rasalas', [
                'type' => 'success',
                'message' => 'Ustawienia zostały prawidłowo zapisane!',
            ]);
        } else {
            $session->getFlashBag()->add('rasalas', [
                'type' => 'danger',
                'message' => 'Wystąpił błąd, ustawienia nie zostały zapisane.',
            ]);
        }

        $targetUrl = $this->app['url_generator']->generate('rasalas');

        return new RedirectResponse($targetUrl);
    }

    /**
     * Saves settings into the extension its config file.
     *
     * @param $request Request
     *
     * @return bool
     */
    protected function saveExtensionSettings(Request $request)
    {
        $settings = array(
            'id' => $request->request->get('rasalas_script_id')
        );

        $extension = $this->app['extensions']->get('Optize/Rasalas');
        $filePath = sprintf('config://extensions/%s.%s.yml', strtolower($extension->getName()), strtolower($extension->getVendor()));
        $configFile = $this->app['filesystem']->get($filePath);

        return $this->saveYamlFile($configFile, $settings);
    }

    /**
     * Saves the Yaml file.
     *
     * @param YamlFile $yamlFile
     * @param array $data
     *
     * @return bool
     */
    protected function saveYamlFile(YamlFile $yamlFile, array $data)
    {
        try {
            $yamlFile->dump($data, [
                'objectSupport' => true,
                'inline' => 7,
            ]);

            return true;
        } catch (DumpException $e) {
            return false;
        }
    }
}
