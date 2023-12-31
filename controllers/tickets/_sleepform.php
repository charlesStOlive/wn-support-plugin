<?= Form::open() ?>

<div class="modal-dialog" id="coin">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button"
                class="close"
                data-dismiss="modal"
                aria-hidden="true">&times;</button>
        <h4 class="modal-title">Confirmez la date de réveil</h4>
    </div>

    <div class="modal-body">
        <input id="modelId" name="modelId" type="hidden" value="<?=$modelId?>">
        <?=  $wfSleepWidget->render() ?>
    </div>

        <div class="modal-footer">
            <button type="submit"
                    data-request="onSaveSleep"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-popup-load-indicator
                    class="btn btn-primary">
                <?=e(trans('waka.wutils::lang.global.validate'))?>
            </button>
            <button type="button"
                    class="btn btn-default"
                    data-dismiss="popup">
                <?=e(trans('waka.wutils::lang.global.termined'))?>
            </button>
        </div>
    </div>
</div>



<?= Form::close() ?>


