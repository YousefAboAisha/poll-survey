<?php
use Shuchkin\SimpleXLSX;
global $wpdb ;

$request = SRP_PLUGIN::get_request();
$app = SRP_PLUGIN::get_app();

$SR_jforms = new jforms();
$SR_redirect_cache = new clogica_SR_redirect_cache();

$export_file = isset($_GET['export_file']) ? $_GET['export_file'] : false;
if($export_file == true)
{

if(current_user_can("manage_options")){

	$group = ($_REQUEST['grpID'] > 0) ? " and grpID=" . intval($_REQUEST['grpID']) : '';
    


    $table_name= SR_database::WP_SEO_Redirection();
	
    $results = $wpdb->get_results("select redirect_from,redirect_to,redirect_type,redirect_from_type,redirect_from_folder_settings,redirect_from_subfolders,redirect_to_type,redirect_to_folder_settings,regex from $table_name where cat='link' $group");

    $cvar[0][0]='redirect_from';
    $cvar[0][1]='redirect_to';
    $cvar[0][2]='redirect_type';
    $cvar[0][3]='redirect_from_type';
    $cvar[0][4]='redirect_from_folder_settings';
    $cvar[0][5]='redirect_from_subfolders';
    $cvar[0][6]='redirect_to_type';
    $cvar[0][7]='redirect_to_folder_settings';
    $cvar[0][8]='regex';

    $i=0;
    foreach($results as $result)
    {
		
        $i++;
        $cvar[$i][0]=$result->redirect_from;
        $cvar[$i][1]=$result->redirect_to;
        $cvar[$i][2]=$result->redirect_type;
        $cvar[$i][3]=$result->redirect_from_type;
        $cvar[$i][4]=$result->redirect_from_folder_settings;
        $cvar[$i][5]=$result->redirect_from_subfolders;
        $cvar[$i][6]=$result->redirect_to_type;
        $cvar[$i][7]=$result->redirect_to_folder_settings;
        $cvar[$i][8]=$result->regex;
    }

    if(isset($_REQUEST['export_file_type']) && $_REQUEST['export_file_type'] == 'xlsx'){
        arr_xlsx($cvar, 'redirects.xlsx');
    }else {
        arr_csv($cvar, 'redirects.csv');
    }

}else{
    echo __("You must login to export!!",'wsr');
}
}


function arr_csv($results, $name = NULL)
{
    if( ! $name)
    {
        $name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
    }
	ob_end_clean(); // this is solution
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename='. $name);
    header('Pragma: no-cache');
    header("Expires: 0");
    $outstream = fopen("php://output", "w");
    foreach($results as $result)
    {
        fputcsv($outstream, $result);
    }
    fclose($outstream);
	exit();
}

function arr_xlsx($results, $name = NULL)
{

    include_once("xlsxwriter.class.php");
    $writer = new XLSXWriter();
    $writer->writeSheet($results);
    $writer->writeToFile('redirects.xlsx');
    $file = "redirects.xlsx";
    ob_end_clean(); // this is solution
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    readfile($file);

}
function filterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}




function csv_arr($file_name)
{
    $arrResult = array();
    $handle = fopen($file_name, "r");
    if( $handle ) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $arrResult[] = $data;
        }
        fclose($handle);
    }
    return $arrResult;
}

function xlsx_arr($file_name)
{
    include_once("SimpleXLSX.php");
    $arrResult = array();
    if ($xlsx = SimpleXLSX::parse($file_name)) {
        foreach ($xlsx->rows() as $val){
            $arrResult[] = $val;
        }
    }

    return $arrResult;
}

function add_csv_mime_upload_mimes( $existing_mimes ){
    $existing_mimes['csv'] = 'application/octet-stream'; //allow CSV files
    $existing_mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; //allow CSV files
    return $existing_mimes;
}

if($request->post('btn_import')!='')
{
    add_filter('upload_mimes', 'add_csv_mime_upload_mimes');

    if(array_key_exists('import_file',$_FILES) && $_FILES['import_file']['name']!='')
    {
        $filename = $_FILES['import_file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(strtolower($ext)=='csv' || strtolower($ext)=='xlsx')
        {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $uploadedfile=$_FILES['import_file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload($uploadedfile,$upload_overrides);
            if ( $movefile && !isset( $movefile['error'] ) ) {
                $app->echo_message(__("File is valid, and was successfully uploaded.",'wsr'));
                if(strtolower($ext)=='csv'){
                    $results=csv_arr($movefile['file']);
                }else{
                    $results=xlsx_arr($movefile['file']);
                }


                // start add to database ----------------------------------

                $index=0;
                if($request->post('col_names','int')!=0) $index++;
                $errors=0;
                $exist=0;
                $new=0;
                $grpID=$request->post('grpID','int');

                for($i=$index;$i<count($results);$i++)
                {
                    $sql="";
                    $redirect_from_type='Page';
                    $redirect_to_type='Page';
                    $redirect_from_folder_settings = '1';
                    $redirect_from_subfolders='0';
                    $redirect_to_folder_settings='1';
                    $redirect_type='301';
                    $regex='';
                    $redirect_from='';
                    $redirect_to='';

                    if(count($results[$i])>0)
                        $redirect_from=$results[$i][0];

                    if(count($results[$i])>1)
                        $redirect_to=$results[$i][1];

                    if(count($results[$i])>2)
                        $redirect_type=$results[$i][2];

                    if(count($results[$i])>3)
                        $redirect_from_type=$results[$i][3];

                    if(count($results[$i])>4)
                        $redirect_from_folder_settings=$results[$i][4];

                    if(count($results[$i])>5)
                        $redirect_from_subfolders=$results[$i][5];

                    if(count($results[$i])>6)
                        $redirect_to_type=$results[$i][6];

                    if(count($results[$i])>7)
                        $redirect_to_folder_settings=$results[$i][7];

                    if(count($results[$i])>8)
                        $regex=$results[$i][8];

                    if($redirect_from!='' && $redirect_to!='' && intval($redirect_type)!=0)
                    {
                        $exec=0;
                        if($wpdb->get_var(" select redirect_from from ". SR_database::WP_SEO_Redirection() ." where redirect_from='$redirect_from' and cat='link' and blog='" . get_current_blog_id() . "' "))
                        {
                            $exist++;
                            if($request->post('rule')=='replace')
                            {
                                $wpdb->get_var(" delete from ". SR_database::WP_SEO_Redirection() ." where redirect_from='$redirect_from' and cat='link' and blog='" . get_current_blog_id() . "' ");
                                $exec=1;
                            }
                        }else
                        {
                            $exec=1;
                            $new++;
                        }
                        
                        if($exec==1){                            
                            $wpdb->insert(SR_database::WP_SEO_Redirection(), array(
                                "redirect_from" => $redirect_from ,
                                "redirect_to" => $redirect_to ,
                                "redirect_type" => $redirect_type ,
                                "redirect_from_type" => $redirect_from_type ,
                                "redirect_from_folder_settings" => $redirect_from_folder_settings ,
                                "redirect_from_subfolders" => $redirect_from_subfolders ,
                                "redirect_to_type" => $redirect_to_type ,
                                "redirect_to_folder_settings" => $redirect_to_folder_settings,
                                "regex" => $regex ,
                                "cat" => 'link' ,
                                "grpID" => $grpID ,
                                "blog" => get_current_blog_id()
                            ));
                        }
                        
                    }else
                    {
                        $errors++;
                    }

                }

                $report= intval($errors+$exist+$new) . " redirects are imported with $errors errors,$new new redirects and $exist are ";
                if($request->post('rule')=='replace')
                {
                    $report= $report . 'replaced!';
                }else
                {
                    $report= $report . 'skipped!';
                }

                $app->echo_message($report);

                // end the entrance to database ---------------------------


                unlink($movefile['file']);
                $app->echo_message(__("File is deleted!",'wsr'));
                $SR_redirect_cache->free_cache();

            } else {
                echo $movefile['error'];
            }

        }else
        {
            $app->echo_message(__("Please choose a CSV file",'wsr'),'danger');
        }

    }else
    {
        $app->echo_message(__("You need to select a file to upload it!",'wsr'),'danger');
    }
}


?>

<h4><?php _e("Export Redirects",'wsr')?></h4><hr/>


    <form id="export" target="_blank" action="options-general.php?page=seo-redirection-premium.php&export=csv&SR_tab=export_import&export_file=true" method="post" class="form-horizontal" role="form" data-toggle="validator">

                <div class="form-group">
                    <label class="control-label col-sm-2" for="export_file_type"><?php _e("Output Type:",'wsr')?></label>
                    <div class="col-sm-5">
                        <?php
                        $drop = new dropdown_list('export_file_type');
                        $drop->add(__('CSV','wsr'), 'csv');
                        $drop->add(__('XLSX','wsr'), 'xlsx');
                        $drop->run($SR_jforms);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="grpID"><?php _e("Redirects:",'wsr')?></label>
                    <div class="col-sm-5">
                        <?php
                        $drop = new dropdown_list('grpID');
                        $drop->add(__('All Groups','wsr'),'');
                        $groups = $wpdb->get_results("select * from `" . SR_database::WP_SEO_Groups() . "` where blog='" . get_current_blog_id() . "'  order by group_type desc;");
                        foreach ( $groups as $group ) {

                            $count= $wpdb->get_var("select count(*) as cnt from `" . SR_database::WP_SEO_Redirection() . "` where cat='link' and blog='" . get_current_blog_id() . "'  and grpID=" . $group->ID);
                            $drop->add($group->group_title . ' (' . $count . ')' ,$group->ID);
                        }
                        $drop->run($SR_jforms);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-12">
                        <input type="hidden" name="blog" value="<?php echo get_current_blog_id()?>"/>
                        <button type="submit" class="btn btn-primary btn-sm" name="btn_export" value="btn_export"><span class="glyphicon glyphicon-export"></span><?php _e("Export",'wsr'); ?></button>
                    </div>
                </div>
    </form>

<h4><?php _e("Import Redirects",'wsr'); ?></h4><hr/>

    <form id="import" name="import" enctype='multipart/form-data' action="<?php echo $request->get_current_parameters(array("add","edit","del"));?>" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" name="MAX_FILE_SIZE" value="999000000" />
        <div class="form-group">
            <label class="control-label col-sm-2" for="import_file_type"><?php _e("File Type:",'wsr') ?></label>
            <div class="col-sm-5">
                <?php
                $drop = new dropdown_list('import_file_type');
                $drop->add(__('CSV','wsr'), 'csv');
                $drop->add(__('XLSX','wsr'), 'xlsx');
                $drop->run($SR_jforms);
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="file"><?php _e("Choose File:",'wsr');?></label>
            <div class="col-sm-3">
               <input class="btn btn-default btn-sm" type="file" accept="text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="import_file" name="import_file" required/>
            </div>
			<div class="col-sm-3">
               <a target="_blank" href="https://wp-buy.com/redirection-kb/topics/seo-redirection-premium/export-import"><?php _e('Export/Import Redirects Tutorial ','wsr');?></a>
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-sm-2" for="grpID"><?php _e("Save to:",'wsr'); ?></label>
            <div class="col-sm-5">
                <?php
                $drop = new dropdown_list('grpID');
                $groups = $wpdb->get_results("select * from `" . SR_database::WP_SEO_Groups() . "` where blog='" . get_current_blog_id() . "'  order by group_type desc;");
                foreach ( $groups as $group ) {

                    $count= $wpdb->get_var("select count(*) as cnt from `" . SR_database::WP_SEO_Redirection() . "` where cat='link' and grpID=" . $group->ID);
                    $drop->add($group->group_title . ' (' . $count . ')' ,$group->ID);
                }
                $drop->run($SR_jforms);
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="Rule"><?php _e("Column Titles:",'wsr')?></label>
            <div class="col-sm-5">
                <?php
                    $check = new bcheckbox_option();
                    $check->create_single_option('col_names',1);
                    echo __(" Skip the first row of the file (if there is a table header)",'wsr');
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="Rule"><?php _e("Import Rule:",'wsr'); ?></label>
            <div class="col-sm-5">
                <?php
                $drop = new dropdown_list('rule');
                $drop->add(__('Skip the existing redirects with the same source','wsr'), 'skip');
                $drop->add(__('Replace the existing redirects with the same source','wsr'), 'replace');
                $drop->run($SR_jforms);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-12">
                <button class="btn btn-primary btn-sm" type="submit" name="btn_import" value="btn_import"><span class="glyphicon glyphicon-import"></span><?php _e("Import",'wsr') ?></button>
            </div>
        </div>
        <br/>
        <div style="text-align: right"><?php _e("* Need Help?",'wsr');?> <a target="_blank" href="https://wp-buy.com/redirection-kb/topics/seo-redirection-premium/export-import"><?php _e("click here to see info about import and export","wsr"); ?></a></div>
        <br/>
    </form>

<?php
$SR_jforms->set_small_select_pickers();
$SR_jforms->hide_alerts(12000);
$SR_jforms->run();
