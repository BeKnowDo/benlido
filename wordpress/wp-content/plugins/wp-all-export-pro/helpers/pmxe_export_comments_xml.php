<?php
/**
*	Export XML helper
*/
function pmxe_export_comments_xml($exportQuery, $exportOptions, $preview = false, $is_cron = false, $file_path = false, $exported_by_cron = 0){
	
	require_once PMXE_ROOT_DIR . '/classes/XMLWriter.php';
	
	$xmlWriter = new PMXE_XMLWriter();
	$xmlWriter->openMemory();
	$xmlWriter->setIndent(true);
	$xmlWriter->setIndentString("\t");
	$xmlWriter->startDocument('1.0', $exportOptions['encoding']);
	$xmlWriter->startElement($exportOptions['main_xml_tag']);		

	if ($is_cron)
	{							
		if ( ! $exported_by_cron )
		{
			$additional_data = apply_filters('wp_all_export_additional_data', array(), $exportOptions);

			if ( ! empty($additional_data))
			{
				foreach ($additional_data as $key => $value) 
				{
					$xmlWriter->startElement(preg_replace('/[^a-z0-9_-]/i', '', $key));
						$xmlWriter->writeData($value);
					$xmlWriter->endElement();		
				}
			}
		}					
	}
	else
	{

		if ( empty(PMXE_Plugin::$session->file) ){

			$additional_data = apply_filters('wp_all_export_additional_data', array(), $exportOptions);

			if ( ! empty($additional_data))
			{
				foreach ($additional_data as $key => $value) 
				{
					$xmlWriter->startElement(preg_replace('/[^a-z0-9_-]/i', '', $key));
						$xmlWriter->writeData($value);
					$xmlWriter->endElement();		
				}
			}
		}			
	}

	foreach ( $exportQuery->get_comments() as $comment ) :	

		$is_export_comment = apply_filters('wp_all_export_xml_rows', true, $comment, $exportOptions);		

		if ( ! $is_export_comment ) continue;			

		//$exportQuery->the_post(); $record = get_post( get_the_ID() );

		$xmlWriter->startElement($exportOptions['record_xml_tag']);			

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

						if (empty($exportOptions['cc_name'][$ID]) or empty($exportOptions['cc_type'][$ID])) continue;
						
						$element_name_ns = '';
						$element_name = ( ! empty($exportOptions['cc_name'][$ID]) ) ? preg_replace('/[^a-z0-9_:-]/i', '', $exportOptions['cc_name'][$ID]) : 'untitled_' . $ID;				
						$fieldSnipped = ( ! empty($exportOptions['cc_php'][$ID]) and ! empty($exportOptions['cc_code'][$ID]) ) ? $exportOptions['cc_code'][$ID] : false;

						if (strpos($element_name, ":") !== false)
						{
							$element_name_parts = explode(":", $element_name);
							$element_name_ns = (empty($element_name_parts[0])) ? '' : $element_name_parts[0];
							$element_name = (empty($element_name_parts[1])) ? 'untitled_' . $ID : preg_replace('/[^a-z0-9_-]/i', '', $element_name_parts[1]);							
						}

						switch ($exportOptions['cc_type'][$ID]) {
							case 'comment_ID':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_id', pmxe_filter($comment->comment_ID, $fieldSnipped), $comment->comment_ID));			
								break;
							case 'comment_post_ID':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_post_id', pmxe_filter($comment->comment_post_ID, $fieldSnipped), $comment->comment_ID));
								break;
							case 'comment_author':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_author', pmxe_filter($comment->comment_author, $fieldSnipped), $comment->comment_ID));
								break;							
							case 'comment_author_email':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_author_email', pmxe_filter($comment->comment_author_email, $fieldSnipped), $comment->comment_ID));
								break;
							case 'comment_author_url':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_author_url', pmxe_filter($comment->comment_author_url, $fieldSnipped), $comment->comment_ID));
								break;
							case 'comment_author_IP':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_author_IP', pmxe_filter($comment->comment_author_IP, $fieldSnipped), $comment->comment_ID));
								break;							
							case 'comment_karma':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_karma', pmxe_filter($comment->comment_karma, $fieldSnipped), $comment->comment_ID));
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
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $comment->comment_ID));
								break;		

							case 'comment_approved':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_approved', pmxe_filter($comment->comment_approved, $fieldSnipped), $comment->comment_ID));
								break;	
							case 'comment_agent':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_agent', pmxe_filter($comment->comment_agent, $fieldSnipped), $comment->comment_ID));
								break;	
							case 'comment_type':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_type', pmxe_filter($comment->comment_type, $fieldSnipped), $comment->comment_ID));
								break;														
							case 'comment_parent':							
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_comment_parent', pmxe_filter($comment->comment_parent, $fieldSnipped), $comment->comment_ID));
								break;
							case 'user_id':							
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_id', pmxe_filter($comment->user_id, $fieldSnipped), $comment->comment_ID));
								break;								
							case 'comment_content':
								$xmlWriter->beginElement($element_name_ns, $element_name, null);
									$xmlWriter->writeData(apply_filters('pmxe_comment_content', pmxe_filter($comment->comment_content, $fieldSnipped), $comment->comment_ID));
								$xmlWriter->endElement();
								break;

							case 'cf':							
								if ( ! empty($exportOptions['cc_value'][$ID]) ){																		
									$cur_meta_values = get_comment_meta($comment->comment_ID, $exportOptions['cc_value'][$ID]);																				
									if (!empty($cur_meta_values) and is_array($cur_meta_values)){
										foreach ($cur_meta_values as $key => $cur_meta_value) {
											$xmlWriter->beginElement($element_name_ns, $element_name, null);
												$xmlWriter->writeData(apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $exportOptions['cc_value'][$ID], $comment->comment_ID));
											$xmlWriter->endElement();
										}
									}

									if (empty($cur_meta_values)){
										$xmlWriter->beginElement($element_name_ns, $element_name, null);
											$xmlWriter->writeData(apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $exportOptions['cc_value'][$ID], $comment->comment_ID));
										$xmlWriter->endElement();
									}																																																												
								}								
								break;																		
							
							case 'sql':

								if ( ! empty($exportOptions['cc_sql'][$ID]) ){

									global $wpdb;
									$val = $wpdb->get_var( $wpdb->prepare( stripcslashes(str_replace("%%ID%%", "%d", $exportOptions['cc_sql'][$ID])), $comment->comment_ID ));
									if ( ! empty($exportOptions['cc_php'][$ID]) and !empty($exportOptions['cc_code'][$ID])){
										// if shortcode defined
										if (strpos($exportOptions['cc_code'][$ID], '[') === 0){									
											$val = do_shortcode(str_replace("%%VALUE%%", $val, $exportOptions['cc_code'][$ID]));
										}	
										else{
											$val = eval('return ' . stripcslashes(str_replace("%%VALUE%%", $val, $exportOptions['cc_code'][$ID])) . ';');
										}										
									}
									$xmlWriter->beginElement($element_name_ns, $element_name, null);
										$xmlWriter->writeData(apply_filters('pmxe_sql_field', $val, $element_name, $comment->comment_ID));
									$xmlWriter->endElement();
								}
								break;							

							default:
								# code...
								break;
						}						
					}					
				}
			endif;		

		$xmlWriter->endElement(); // end post		
		
		if ($preview) break;

		do_action('pmxe_exported_comment', $comment->comment_ID );

	endforeach;

	$xmlWriter->endElement(); // end data
	
	if ($preview) return $xmlWriter->flush(true); //wp_all_export_remove_colons($xmlWriter->flush(true));	

	if ($is_cron)
	{		
		
		$xml = $xmlWriter->flush(true);

		if ( ! $exported_by_cron )
		{
			// The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.
			if ($exportOptions['include_bom']) 
			{
				file_put_contents($file_path, chr(0xEF).chr(0xBB).chr(0xBF).substr($xml, 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)));
			}
			else
			{
				file_put_contents($file_path, substr($xml, 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)));
			}			
		}
		else
		{
			file_put_contents($file_path, substr(substr($xml, 41 + strlen($exportOptions['main_xml_tag'])), 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)), FILE_APPEND);
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
				file_put_contents($export_file, chr(0xEF).chr(0xBB).chr(0xBF).substr($xmlWriter->flush(true), 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)));
			}
			else
			{
				file_put_contents($export_file, substr($xmlWriter->flush(true), 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)));
			}

			PMXE_Plugin::$session->set('file', $export_file);
			
			PMXE_Plugin::$session->save_data();

		}	
		else
		{
			file_put_contents(PMXE_Plugin::$session->file, substr(substr($xmlWriter->flush(true), 41 + strlen($exportOptions['main_xml_tag'])), 0, (strlen($exportOptions['main_xml_tag']) + 4) * (-1)), FILE_APPEND);
		}

		return true;

	}	

}