<?php
$queryTerm = get_search_query();
// this is product search
$total = 0;
if ( wc_get_loop_prop( 'total' ) ) {
    $total = wc_get_loop_prop( 'total' );
}
$total = intval($total);

$data = array('queryTerm'=>$queryTerm,'count'=>$total,'products'=>true);
Timber::render( 'common/navigation/search-in-page.twig',$data);

