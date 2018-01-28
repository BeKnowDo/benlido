<?php
/*
Plugin Name: Ben Lido Product Import
Plugin URI: http://www.benlido.com/
Description: Import products from a spreadsheet and match to ASIN
Version: 1.0
*/

global $bl_product_import_admin_slug;
$bl_product_import_admin_slug = 'bl-product-import';

function bl_product_import_settings() {
    global $bl_product_import_admin_slug;
    $message = '';
    if (!empty($_POST)) {
        $tmp_name = $_FILES['bl_inv_import']['tmp_name'];
        if (!empty($tmp_name)) {
            if (function_exists('bl_parse_csv')) {

            }
        }
    } // end $_POST

?>
    <form method="post" enctype="multipart/form-data" action="admin.php?page=<?php echo $bl_product_import_admin_slug?>">
    <h3><span>Product Import</span></h3>
    <div class="postbox">
      <div class="inside">
        <?php echo $message ?>
        <p>NOTE: 
            Please use the "Save as" functionality in Excel, and save the spreadsheet as a "csv". If the spreadsheet is not saved as a "csv", the import will not work.
        </p>
        <label for="blush_inv_import">Import Products:</label><br />
        <input type="file" id="bl_inv_import" name="bl_inv_import" /><br />
        <input type="submit" name="upload" value="Import" />
      </div>
    </div>
</form>
<?php

} // end bl_product_import_settings()

function bl_product_import_admin() {
    global $bl_product_import_admin_slug;
    add_options_page('Ben Lido Product Import', 'Product Import', 'manage_options', $bl_product_import_admin_slug, 'bl_product_import_settings');
}
  
add_action('admin_menu', 'bl_product_import_admin');

if (!function_exists('bl_parse_csv')) {


function bl_parse_csv($csv)
{
        $row = 0;
        $keys = array();
        $csvData = array();
        ini_set('auto_detect_line_endings',TRUE);
        if (($handle = fopen($csv, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 2500, ",")) !== FALSE) {
                        $row++;
                        if ($row == 1) {
                                $keys = $data;
                                foreach ($keys as $keyIdx => $key) {
                                    $key = $this->remove_utf8_bom($key);
                                    $key = str_replace('.','',strtolower($key));
                                        $keys[$keyIdx] = str_replace(' ','_',trim(strtolower($key)));
                                }
                                continue;
                        }

                        $num = count($data);

                        if (empty($data[0]))
                                continue;

                        $rowData = array();
                        for ($c = 0; $c < $num; $c++) {
                                $rowData[$keys[$c]] = $data[$c];
                        }

                        $csvData[] = $rowData;
                }
                fclose($handle);
        }

        return $csvData;


} // end bl_parse_csv()

} // end if not function_exists