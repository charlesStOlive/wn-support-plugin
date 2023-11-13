<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
/**
 * Tickets Backend Controller
 */
class Tickets extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
        \Waka\Wutils\Behaviors\WakaControllerBehavior::class,
        \Waka\Productor\Behaviors\ProductorBehavior::class,
        \Waka\Productor\Behaviors\ProductorIndexBehavior::class,
        \Waka\Wutils\Behaviors\WakaReorderController::class,
        \Waka\Workflow\Behaviors\WorkflowBehavior::class,
    ];

    public $requiredPermissions = ['waka.support.*'];

    public $wfSleepWidget;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'side-menu-tickets');
        //
        $this->addCss('/plugins/waka/support/assets/css/messages_list.css');
        $this->wfSleepWidget = $this->createSleepFormWidget();
    }

    
    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function onCreateNewFromArchived() {
        $ticket = \Waka\Support\Models\Ticket::find($this->params[0]);
        $newId = $ticket->createChildTicket();
        return \Redirect::to(\Backend::url('waka/support/tickets/update/'.$newId));
    }

    public function onAfterSaveWorkflowJs() {
        $wfPopupAfterSave = \Session::pull('popup_afterSave');
        if(!$wfPopupAfterSave) {
            return true;
        }
        if($this->params[0] ?? false) {
            $this->vars['modelId'] = $this->params[0];
            $this->vars['wfSleepWidget'] = $this->wfSleepWidget;
            //trace_log("make partial");
            return $this->makePartial('sleepform');
        }
       
    }

    public function onTestCron() {
        $sleepIngTickets = \Waka\Support\Models\Ticket::where('state', 'sleep')->whereDate('awake_at' ,'<', \Carbon\Carbon::now());
        //trace_log($sleepIngTickets->count());
        foreach($sleepIngTickets->get() as $ticketToOpen) {
            //trace_log($ticketToOpen->wakaWorkflowCan('sleep_to_wait_support'));
            if($ticketToOpen->wakaWorkflowCan('sleep_to_wait_support')) {
                $ticketToOpen->workflow_apply('sleep_to_wait_support');
                $ticketToOpen->save();
            }
            //trace_log($ticketToOpen->wakaWorkflowCan('sleep_to_wait_managment'));
            if($ticketToOpen->wakaWorkflowCan('sleep_to_wait_managment')) {
                $ticketToOpen->workflow_apply('sleep_to_wait_managment');
                $ticketToOpen->save();
            }
        }
        //trace_log('call support');
    }

    public function onSaveSleep() {
        $ticket = \Waka\Support\Models\Ticket::find(post('modelId'));
        $ticket->awake_at = post('wf_sleep_array.awake_at');
        $ticket->save();
        return \Redirect::to(\Backend::url('waka/support/tickets'));
    }
    public function createSleepFormWidget() {
        $config = $this->makeConfig('$/waka/support/models/ticket/fields_sleep.yaml');
        $config->alias = 'wfSleepWidget';
        $config->arrayName = 'wf_sleep_array';
        $config->model = new \Waka\Support\Models\Ticket();
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
