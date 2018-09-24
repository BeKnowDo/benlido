<?php
/**
*	Export XML helper
*/
function pmxe_export_users_xml($exportQuery, $exportOptions, $preview = false, $is_cron = false, $file_path = false, $exported_by_cron = 0){
	
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

	foreach ( $exportQuery->results as $user ) :				

		//$exportQuery->the_post(); $record = get_post( get_the_ID() );

		$is_export_user = apply_filters('wp_all_export_xml_rows', true, $user, $exportOptions);		

		if ( ! $is_export_user ) continue;

		$xmlWriter->startElement($exportOptions['record_xml_tag']);			

			if ($exportOptions['ids']):		

				if ( wp_all_export_is_compatible() and $exportOptions['is_generate_import'] and $exportOptions['import_id']){	
					$postRecord = new PMXI_Post_Record();
					$postRecord->clear();
					$postRecord->getBy(array(
						'post_id' => $user->ID,
						'import_id' => $exportOptions['import_id'],
					));

					if ($postRecord->isEmpty()){
						$postRecord->set(array(
							'post_id' => $user->ID,
							'import_id' => $exportOptions['import_id'],
							'unique_key' => $user->ID							
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
							case 'id':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_id', pmxe_filter($user->ID, $fieldSnipped), $user->ID));			
								break;
							case 'user_login':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_login', pmxe_filter($user->user_login, $fieldSnipped), $user->ID));
								break;
							case 'user_pass':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_pass', pmxe_filter($user->user_pass, $fieldSnipped), $user->ID));
								break;							
							case 'user_email':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_email', pmxe_filter($user->user_email, $fieldSnipped), $user->ID));
								break;
							case 'user_nicename':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_nicename', pmxe_filter($user->user_nicename, $fieldSnipped), $user->ID));
								break;
							case 'user_url':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_url', pmxe_filter($user->user_url, $fieldSnipped), $user->ID));
								break;
							/*case 'user_activation_key':
								$xmlWriter->writeElement($element_name, apply_filters('pmxe_user_activation_key', pmxe_filter($user->user_activation_key, $fieldSnipped), $user->ID));
								break;
							case 'user_status':
								$xmlWriter->writeElement($element_name, apply_filters('pmxe_user_status', pmxe_filter($user->user_status, $fieldSnipped), $user->ID));
								break;*/
							case 'display_name':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_display_name', pmxe_filter($user->display_name, $fieldSnipped), $user->ID));
								break;							
							case 'user_registered':
								if (!empty($exportOptions['cc_options'][$ID])){ 
									switch ($exportOptions['cc_options'][$ID]) {
										case 'unix':
											$post_date = strtotime($user->user_registered);
											break;										
										default:
											$post_date = date($exportOptions['cc_options'][$ID], strtotime($user->user_registered));
											break;
									}									
								}
								else{
									$post_date = $user->user_registered; 
								}
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_registered', pmxe_filter($post_date, $fieldSnipped), $user->ID));
								break;		

							case 'nickname':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_nickname', pmxe_filter($user->nickname, $fieldSnipped), $user->ID));
								break;	
							case 'first_name':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_first_name', pmxe_filter($user->first_name, $fieldSnipped), $user->ID));
								break;	
							case 'last_name':
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_last_name', pmxe_filter($user->last_name, $fieldSnipped), $user->ID));
								break;														
							case 'wp_capabilities':							
								$xmlWriter->putElement($element_name_ns, $element_name, null, apply_filters('pmxe_user_wp_capabilities', pmxe_filter(implode(",", $user->roles), $fieldSnipped), $user->ID));
								break;								
							case 'description':
								$xmlWriter->beginElement($element_name_ns, $element_name, null);
									$xmlWriter->writeData(apply_filters('pmxe_user_description', pmxe_filter($user->description, $fieldSnipped), $user->ID));
								$xmlWriter->endElement();
								break;

							case 'cf':							
								if ( ! empty($exportOptions['cc_value'][$ID]) ){																		
									$cur_meta_values = get_user_meta($user->ID, $exportOptions['cc_value'][$ID]);																				
									if (!empty($cur_meta_values) and is_array($cur_meta_values)){
										foreach ($cur_meta_values as $key => $cur_meta_value) {
											$xmlWriter->beginElement($element_name_ns, $element_name, null);
												$xmlWriter->writeData(apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $exportOptions['cc_value'][$ID], $user->ID));
											$xmlWriter->endElement();
										}
									}

									if (empty($cur_meta_values)){
										$xmlWriter->beginElement($element_name_ns, $element_name, null);
											$xmlWriter->writeData(apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $exportOptions['cc_value'][$ID], $user->ID));
										$xmlWriter->endElement();
									}																																																												
								}								
								break;

							case 'acf':							

								if ( ! empty($exportOptions['cc_label'][$ID]) and class_exists( 'acf' ) ){		

									global $acf;

									$field_value = get_field($exportOptions['cc_label'][$ID], 'user_' . $user->ID);

									$field_options = unserialize($exportOptions['cc_options'][$ID]);

									pmxe_export_acf_field_xml($field_value, $exportOptions, $ID, 'user_' . $user->ID, $xmlWriter, $element_name, $element_name_ns, $fieldSnipped, $field_options['group_id']);
																																																																					
								}				
												
								break;													
							
							case 'sql':

								if ( ! empty($exportOptions['cc_sql'][$ID]) ){

									global $wpdb;
									$val = $wpdb->get_var( $wpdb->prepare( stripcslashes(str_replace("%%ID%%", "%d", $exportOptions['cc_sql'][$ID])), $user->ID ));
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
										$xmlWriter->writeData(apply_filters('pmxe_sql_field', $val, $element_name, $user->ID));
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

		do_action('pmxe_exported_post', $user->ID );

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