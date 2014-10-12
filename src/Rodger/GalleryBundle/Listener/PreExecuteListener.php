<?php
namespace Rodger\GalleryBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class PreExecuteListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $controllers = $event->getController();
            // контроллер должен существовать
            if (isset($controllers[0])) {
                $controller = $controllers[0];
                // метод должен существовать
                if (method_exists($controller, 'preExecute')) {
                    $controller->preExecute();
                }
            }
        }
    }
}

?>
