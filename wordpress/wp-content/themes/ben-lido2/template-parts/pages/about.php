<?php
  $content = array();
  //$preHeaderText = get_the_title();
  if (function_exists('get_field')) {
  }
  $content = array('preHeaderText' => $preHeaderText);
  $data = array('heroData' => $content);
  Timber::render('pages/about-us.twig',$data);