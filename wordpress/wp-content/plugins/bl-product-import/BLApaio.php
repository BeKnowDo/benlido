<?php
/**
 * define('AMAZON_ACCESS_KEY', 'AKIAIYNHSACAM4TRAEOQ');
 * define('AMAZON_SECRET_KEY', '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');
 * define('AMAZON_ASSOCIATE_TAG', 'caistit-20');
 */

    require_once ( plugin_dir_path(__FILE__) . '/vendor/autoload.php' );
    use ApaiIO\Configuration\GenericConfiguration;
    use ApaiIO\Operations\Search;
    use ApaiIO\Operations\Lookup;
    use ApaiIO\ApaiIO;

class BLApaio {

    public $apaio;

    public function __construct($access_key,$secret_key,$associate_tag) {

        // NOTE: U.S. Only for now
        // $country is the availability region
        $country = 'com'; // us => com
        $conf = new GenericConfiguration();
        $client = new \GuzzleHttp\Client();
        $request = new \ApaiIO\Request\GuzzleRequest($client);
        $conf
        ->setCountry($country)
        ->setAccessKey($access_key)
        ->setSecretKey($secret_key)
        ->setAssociateTag($associate_tag)
        ->setRequest($request)
        ->setResponseTransformer(new \ApaiIO\ResponseTransformer\XmlToArray());
        $this->apaio = new ApaiIO($conf);

    }


    public function _getByASIN($id) {
        $item = array();
        $lookup = new Lookup();
        $lookup->setItemId($id);
        $lookup->setResponseGroup(array('Large'));
        try {
            $res = $this->apaio->runOperation($lookup);
        } catch (Exception $e) {
            error_log($e);
        }
        
        if (!empty($res)) {
            $item = $res['Items']['Item'];
        }
        //print_r ($item);
        if (!empty($item) && !empty($item[0]['ASIN'])) {
            $item = $item[0];
        }
        return $item;
    } // end _getByASIN();

    public function _getByUPC($upc) {
        $item = array();
        $lookup = new Lookup();
        $lookup->setItemId($upc);
        $lookup->setIdType('UPC');
        $lookup->setResponseGroup(array('Large'));
        try {
            $res = $this->apaio->runOperation($lookup);
        } catch (Exception $e) {
            //print_r ($e);
            error_log($e);
        }

        if (!empty($res)) {
            $item = $res['Items']['Item'];
        }
        if (!empty($item) && !empty($item[0]['ASIN'])) {
            $item = $item[0];
        }
        if (empty($item) && !empty($res['Items']['Request']['Errors'])) {
            $item = array('error'=>$res['Items']['Request']['Errors']['Error']['Message']);
        }
        return $item;
    }

    public function _search($name,$size_label=null) {
        $search = new Search();
        $term = $name . ' ' . $size_label;
        $search->setKeywords($term);   
        $search->setCategory('All'); //
        $search->setResponseGroup(array('Large', 'Images','ItemAttributes','Offers'));
        if (!empty($this->apaio)) {
            $resp = $this->apaio->runOperation($search);
        }

        if (!empty($resp) && is_array($resp)) {
            $item = $resp['Items']['Item'];
        }
        if (!empty($item) && !empty($item[0]['ASIN'])) {
            $item = $item[0];
        }
        return $item;
    } // end _search()
} // end BLApaio
