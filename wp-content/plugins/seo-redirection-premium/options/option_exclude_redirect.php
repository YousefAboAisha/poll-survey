<?php
if(isset($_POST) && !empty($_POST)){
    update_option('list_of_excluded',sanitize_textarea_field($_POST['urls']));
}
?>

<h4><?php _e('List of excluded URL\'s','wsr');?></h4><hr/>
    <div class="form-group">
        <div class="col-sm-8">

            <form method="post" action="#" class="form-horizontal" role="form" data-toggle="validator">

                <div class="form-group">
                    <label class="control-label col-sm-2" for="enabled"><?php _e('Redirect Url\'s:','wsr'); ?></label>
                    <div class="col-sm-10">
                        <textarea name="urls" class="form-control" rows="3"><?php esc_html_e(get_option('list_of_excluded'));?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <br/>
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="save" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save','wsr'); ?></button>
                    </div>
                    <br/><br/>
                </div>
            </form>

        </div>
        <br/>
    </div>
