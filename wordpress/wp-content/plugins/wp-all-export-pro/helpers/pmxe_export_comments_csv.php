<?php
/**
*	Export CSV helper
*/
function pmxe_export_comments_csv($exportQuery, $exportOptions, $preview = false, $is_cron = false, $file_path = false, $exported_by_cron = 0){

	ob_start();		

	// Prepare headers

	$headers = array();

	$stream = fopen("php://output", 'w');

	$max_attach_count = 0;
	$max_images_count = 0;						

	$cf = array();
	$woo = array();
	$acfs = array();
	$taxes = array();
	$attributes = array();
	$articles = array();

	$implode_delimiter = ($exportOptions['delimiter'] == ',') ? '|' : ',';		

	foreach ( $exportQuery->get_comments() as $comment ) :				

		$attach_count = 0;
		$images_count = 0;								
		$article = array();
		
		$article['ID'] = apply_filters('pmxe_comment_id', $comment->comment_ID);

		if ($exportOptions['ids']):

			if ( wp_all_export_is_compatible() and $exportOptions['is_generate_import'] and $exportOptions['import_id']){	
				$postRecord = new PMXI_Post_Record();
				$postRecord->clear();
				$postRecord->getBy(array(
					'post_id' => $comment->comment_ID,
					'import_id' => $exportOptions['import_id'],
				));

				if ($postRecord->isEmpty()){
					$postRecord->set(array(
						'post_id' => $comment->comment_ID,
						'import_id' => $exportOptions['import_id'],
						'unique_key' => $comment->comment_ID						
					))->save();
				}
				unset($postRecord);
			}			

			foreach ($exportOptions['ids'] as $ID => $value) {				

				if (is_numeric($ID)){ 

					if ( empty($exportOptions['cc_name'][$ID])  or empty($exportOptions['cc_type'][$ID]) ) continue;
					
					$element_name = ( ! empty($exportOptions['cc_name'][$ID]) ) ? $exportOptions['cc_name'][$ID] : 'untitled_' . $ID;
					$fieldSnipped = ( ! empty($exportOptions['cc_php'][$ID] ) and ! empty($exportOptions['cc_code'][$ID])) ? $exportOptions['cc_code'][$ID] : false;

					switch ($exportOptions['cc_type'][$ID]){						
						case 'comment_ID':							
							$article[$element_name] = apply_filters('pmxe_comment_id', pmxe_filter($comment->comment_ID, $fieldSnipped), $comment->comment_ID);				
							break;
						case 'comment_post_ID':
							$article[$element_name] = apply_filters('pmxe_comment_post_id', pmxe_filter($comment->comment_post_ID, $fieldSnipped), $comment->comment_ID);
							break;
						case 'comment_author':
							$article[$element_name] = apply_filters('pmxe_comment_author', pmxe_filter($comment->comment_author, $fieldSnipped), $comment->comment_ID);													
							break;							
						case 'comment_author_email':
							$article[$element_name] = apply_filters('pmxe_comment_author_email', pmxe_filter($comment->comment_author_email, $fieldSnipped), $comment->comment_ID);							
							break;
						case 'comment_author_url':
							$article[$element_name] = apply_filters('pmxe_comment_author_url', pmxe_filter($comment->comment_author_url, $fieldSnipped), $comment->comment_ID);													
							break;
						case 'comment_author_IP':
							$article[$element_name] = apply_filters('pmxe_comment_author_ip', pmxe_filter($comment->comment_author_IP, $fieldSnipped), $comment->comment_ID);						
							break;										
						case 'comment_karma':			
							$article[$element_name] = apply_filters('pmxe_comment_karma', pmxe_filter($comment->comment_karma, $fieldSnipped), $comment->comment_ID);							
							break;	
						case 'comment_content':
							$val = apply_filters('pmxe_comment_content', pmxe_filter($comment->comment_content, $fieldSnipped), $comment->comment_ID);
							$article[$element_name] = ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val;							
							break;		
						case 'comment_date':							
							if (!empty($exportOptions['cc_options'][$ID])){ 								
								switch ($exportOptions['cc_options'][$ID]) {
									case 'unix':
										$post_date = strtotime($comment->comment_date);
										break;									
									default:
										$post_date = date($exportOptions['cc_options'][$ID], strtotime($comment->comment_date));
										break;
								}														
							}
							else{
								$post_date = $comment->comment_date;
							}

							$article[$element_name] = apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $comment->comment_ID); 

							break;						

						case 'comment_approved':
							$article[$element_name] = apply_filters('pmxe_comment_approved', pmxe_filter($comment->comment_approved, $fieldSnipped), $comment->comment_ID);									
							break;	
						case 'comment_agent':
							$article[$element_name] = apply_filters('pmxe_comment_agent', pmxe_filter($comment->comment_agent, $fieldSnipped), $comment->comment_ID);								
							break;	
						case 'comment_type':
							$article[$element_name] = apply_filters('pmxe_comment_type', pmxe_filter($comment->comment_type, $fieldSnipped), $comment->comment_ID);								
							break;													
						case 'comment_parent':							
							$article[$element_name] = apply_filters('pmxe_comment_parent', pmxe_filter($comment->comment_parent, $fieldSnipped), $comment->comment_ID);													
							break;
						case 'user_id':							
							$article[$element_name] = apply_filters('pmxe_user_id', pmxe_filter($comment->user_id, $fieldSnipped), $comment->comment_ID);													
							break;							
						case 'cf':							
							if ( ! empty($exportOptions['cc_value'][$ID]) ){																		
								$cur_meta_values = get_comment_meta($comment->comment_ID, $exportOptions['cc_value'][$ID]);
								if (!empty($cur_meta_values) and is_array($cur_meta_values)){									
									foreach ($cur_meta_values as $key => $cur_meta_value) {										
										if (empty($article[$element_name])){
											$article[$element_name] = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $exportOptions['cc_value'][$ID], $comment->comment_ID);										
											if (!in_array($element_name, $cf)) $cf[] = $element_name;
										}
										else{
											$article[$element_name] = apply_filters('pmxe_custom_field', pmxe_filter($article[$element_name] . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $exportOptions['cc_value'][$ID], $comment->comment_ID);
										}
									}
								}		

								if (empty($cur_meta_values)){
									if (empty($article[$element_name])){
										$article[$element_name] = apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $exportOptions['cc_value'][$ID], $comment->comment_ID);										
										if (!in_array($element_name, $cf)) $cf[] = $element_name;
									}
									// else{
									// 	$article[$element_name . '_' . $key] = apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $exportOptions['cc_value'][$ID], get_the_ID());
									// 	if (!in_array($element_name . '_' . $key, $cf)) $cf[] = $element_name . '_' . $key;
									// }
								}																																																																
							}	
							break;											
							
						case 'sql':							
							if ( ! empty($exportOptions['cc_sql'][$ID]) ) {
								global $wpdb;											
								$val = $wpdb->get_var( $wpdb->prepare( stripcslashes(str_replace("%%ID%%", "%d", $exportOptions['cc_sql'][$ID])), $comment->comment_ID ));
								if ( ! empty($exportOptions['cc_php'][$ID]) and !empty($exportOptions['cc_code'][$ID]) ){
									// if shortcode defined
									if (strpos($exportOptions['cc_code'][$ID], '[') === 0){									
										$val = do_shortcode(str_replace("%%VALUE%%", $val, $exportOptions['cc_code'][$ID]));
									}	
									else{
										$val = eval('return ' . stripcslashes(str_replace("%%VALUE%%", $val, $exportOptions['cc_code'][$ID])) . ';');
									}										
								}
								$article[$element_name] = apply_filters('pmxe_sql_field', $val, $element_name, $comment->comment_ID);		
							}
							break;
						
						default:
							# code...
							break;
					}															
				}				
			}
		endif;		

		$articles[] = $article;

		$articles = apply_filters('wp_all_export_csv_rows', $articles, $exportOptions);	
		
		if ($preview) break;

		do_action('pmxe_exported_post', $comment->comment_ID );

	endforeach;

	if ($exportOptions['ids']):

		foreach ($exportOptions['ids'] as $ID => $value) {

			if (is_numeric($ID)){ 

				if (empty($exportOptions['cc_name'][$ID]) or empty($exportOptions['cc_type'][$ID])) continue;

				$element_name = ( ! empty($exportOptions['cc_name'][$ID]) ) ? $exportOptions['cc_name'][$ID] : 'untitled_' . $ID;

				switch ($exportOptions['cc_type'][$ID]) {	

					case 'cf':

						if ( ! empty($cf) ){
							$headers[] = array_shift($cf);									
						}
						
						break;										
					
					default:
						$headers[] = $element_name;												
						break;
				}							
				
			}			
		}

		if ( is_array($article) ) {
			foreach ( $article as $article_key => $article_item ) {
				if ( ! in_array($article_key, $headers)) $headers[] = $article_key;
			}
		}

	endif;
	
	if ($is_cron)
	{
		if ( ! $exported_by_cron ) fputcsv($stream, $headers, $exportOptions['delimiter']);	
	}
	else
	{
		if ($preview or empty(PMXE_Plugin::$session->file)) fputcsv($stream, $headers, $exportOptions['delimiter']);		
	}
	

	foreach ($articles as $article) {
		$line = array();
		foreach ($headers as $header) {
			$line[$header] = ( isset($article[$header]) ) ? $article[$header] : '';	
		}	
		fputcsv($stream, $line, $exportOptions['delimiter']);
	}			

	if ($preview) return ob_get_clean();	

	if ($is_cron)
	{
		// include BOM to export file
		if ( ! $exported_by_cron )
		{
			// The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.					
			if ($exportOptions['include_bom']) 
			{
				file_put_contents($file_path, chr(0xEF).chr(0xBB).chr(0xBF).ob_get_clean());
			}
			else
			{
				file_put_contents($file_path, ob_get_clean());
			}			
		}
		else
		{
			file_put_contents($file_path, ob_get_clean(), FILE_APPEND);
		}		

		return $file_path;

	}
	else
	{
		if ( empty(PMXE_Plugin::$session->file) ){		

			// generate export file name
			$export_file = wp_all_export_generate_export_file( XmlExportEngine::$exportID );			

			// The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.					
			if ($exportOptions['include_bom']) 
			{
				file_put_contents($export_file, chr(0xEF).chr(0xBB).chr(0xBF).ob_get_clean());
			}
			else
			{
				file_put_contents($export_file, ob_get_clean());
			}

			PMXE_Plugin::$session->set('file', $export_file);
			
			PMXE_Plugin::$session->save_data();

		}	
		else
		{
			file_put_contents(PMXE_Plugin::$session->file, ob_get_clean(), FILE_APPEND);
		}

		return true;
	}
	
}
