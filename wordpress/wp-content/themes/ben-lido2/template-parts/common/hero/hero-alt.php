<?php
$data = array();
$homePageHeroes = array();
// uses the featured cards section
if (function_exists('get_field')) {
    $left_feature_cards = get_field('left_feature_cards');
    $right_feature_cards = get_field('right_feature_cards');
    // this is made to be a carousel.. for now, we're just taking the first one

}
// if (!empty($left_feature_cards) && is_array($left_feature_cards)) {
//     $left_feature_card = $left_feature_cards[0];
// }
// if (!empty($right_feature_cards) && is_array($right_feature_cards)) {
//     $right_feature_card = $right_feature_cards[0];
// }


$leftCardData = [];
$rightCardData = [];
$homePageLeftHeroes = [];
$homePageRightHeroes = [];

foreach($left_feature_cards as $item){
  $newArray = [];

  if (isset($item['image'])) {
      $newArray['image'] = $item['image']['url'];
  }

  if (isset($item['title'])) {
      $newArray['header'] = $item['title'];
  }

  if (isset($item['copy'])) {
      $newArray['copy'] = $item['copy'];
  }

  if (isset($item['button_copy'])) {
      $newArray['button_copy'] = $item['button_copy'];
  }

  if (isset($item['button_url'])) {
      $newArray['button_url'] = $item['button_url'];
  }

  array_push($homePageLeftHeroes, $newArray);
}

foreach($right_feature_cards as $item){
  $newArray = [];

  if (isset($item['image'])) {
      $newArray['image'] = $item['image']['url'];
  }

  if (isset($item['title'])) {
      $newArray['header'] = $item['title'];
  }

  if (isset($item['copy'])) {
      $newArray['copy'] = $item['copy'];
  }

  if (isset($item['button_copy'])) {
      $newArray['button_copy'] = $item['button_copy'];
  }

  if (isset($item['button_url'])) {
      $newArray['button_url'] = $item['button_url'];
  }

  array_push($homePageRightHeroes, $newArray);
}

// if($right_feature_card['title'] == NULL || $left_feature_card['title'] == NULL) {
//     $left_feature_card['singleColumn'] = 'true';
//     $right_feature_card['singleColumn'] = 'true';
// } else {
//     $left_feature_card['singleColumn'] = 'false';
//     $right_feature_card['singleColumn'] = 'false';
// }


// if (!empty($left_feature_card)) {
//     if (isset($left_feature_card['image'])) {
//         $left_feature_card['image'] = $left_feature_card['image']['url'];
//     }
//     if (isset($left_feature_card['title'])) {
//         $left_feature_card['header'] = $left_feature_card['title'];
//     }
// }

// if (!empty($right_feature_card)) {
//     if (isset($right_feature_card['image'])) {
//         $right_feature_card['image'] = $right_feature_card['image']['url'];
//     }
//     if (isset($right_feature_card['title'])) {
//         $right_feature_card['header'] = $right_feature_card['title'];
//     }
// }

// $homePageHeroes = array( $left_feature_card, $right_feature_card);
// $homePageHeroes = array( $leftCardData, $rightCardData);
// echo '<br/><br/><pre>' . var_export($right_feature_card['title'], true) . '</pre>';

if (!empty($homePageLeftHeroes)) {
    $data['homePageLeftHeroes'] = $homePageLeftHeroes;
}

if (!empty($homePageRightHeroes)) {
    $data['homePageRightHeroes'] = $homePageRightHeroes;
}

if (empty($data)) {
    $data = array(
        'homePageLeftHeroes' => array(
            array(
                'header' => 'How it Works',
                'copy' => "Premium, personalized bags packed with the products you love, delivered anywhere, with easy subscription reorders.",
                'image' => '/images/hero-1.png'
            )
        ),
        'homePageRightHeroes' => array(
            array(
                'header' => 'How it Works',
                'copy' => "Premium, personalized bags packed with the products you love, delivered anywhere, with easy subscription reorders.",
                'image' => '/images/hero-1.png'
            )
        )
    );
}

Timber::render( 'common/hero/hero-alt.twig', $data);
