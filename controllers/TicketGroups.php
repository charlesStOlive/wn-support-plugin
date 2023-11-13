<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager; 
/**
 * Ticket Groups Backend Controller
 */
class TicketGroups extends Controller
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
    ];

    public $requiredPermissions = ['waka.support.*'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'side-menu-ticketgroups');
    }

    
    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function listInjectRowClass($record, $value)
    {
        if(!$this->user->hasAccess('waka.support.admin.super')) {
            return 'nolink  disabled';
        }
    }

    public function onClotureTicketGroup() {
        $model =  \Waka\Support\Models\TicketGroup::find($this->params[0]);
        //trace_log($model->name);
        $countRunning = $model->tickets()->where('state', 'running')->count();
        //trace_log($countRunning);
        //trace_log($model->tickets()->where('state', 'running')->get()->toArray());
        if($countRunning)  {
            \Flash::error('Un ticket est en cours de production impossible de cloturer le groupe');
             return \Redirect::refresh();
        } 
        foreach($model->tickets as $ticket) {
            if($ticket->wakaWorkflowCan('to_archived_factu')) {
                //trace_log('ticket a archiver et à recreer : '.$ticket->name);
                $ticket->workflow_apply('to_archived_factu');
                $ticket->save();
            } else {
                //trace_log('ticket déjà  archiver : '.$ticket->name);
            }
        }
        $model->is_factured = true;
        $model->save();
        $redirectUrl = $this->formGetRedirectUrl('update-close', $model);
        //trace_log($redirectUrl);
        \Flash::success('groupe de tickets cloturé !');
        return \Backend::redirect($redirectUrl);
    }
}
