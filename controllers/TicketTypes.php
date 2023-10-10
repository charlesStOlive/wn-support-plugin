<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager; 
/**
 * Ticket Types Backend Controller
 */
class TicketTypes extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Waka\Wutils\Behaviors\WakaControllerBehavior::class,
        \Waka\Wutils\Behaviors\WakaReorderController::class,
    ];

    public $requiredPermissions = ['waka.support.*'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Winter.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Support', 'ticketTypes');
    }

    
    
}
