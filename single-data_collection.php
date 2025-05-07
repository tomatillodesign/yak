<?php

//* Remove the entry meta in the entry header (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

add_action('genesis_entry_content', 'clb_publish_data_collection_metabox', 6);
function clb_publish_data_collection_metabox() {

     $post_id = get_the_ID();

     // Fields
     $brief_description = get_field('brief_measure_description');
     if( $brief_description ) {
          $brief_description = '<div class="clb-single-dct-description-text-wrapper"><p>' . $brief_description . '</p></div>';
     }

     $number_of_items = get_field('number_of_items');
     if( $number_of_items ) {
          $number_of_items = '<div class="clb-single-dct-infoline"><i class="fa-light fa-list-check"></i> Number of Items: ' . $number_of_items . '</div>';
     }

     $measure_adaptations = get_field('measure_adaptations');
     if( $measure_adaptations ) {
          $measure_adaptations = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-wrench-simple"></i> Adaptations: ' . $measure_adaptations . '</div>';
     }

     $relationship = get_field('relationship');
     if( $relationship ) {
          $relationship = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-people-simple"></i> Relationship Focus: ' . implode(', ', $relationship) . '</div>';
     }

     $application_setting = get_field('applicationprior_usesetting_used');
     if( $application_setting ) {
          $application_setting = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-house"></i> Setting: ' . implode(', ', $application_setting) . '</div>';
     }

     $general_scoring_info = get_field('general_scoring_info');
     if( $general_scoring_info ) {
          $general_scoring_info = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-chart-simple"></i> Scoring Info: ' . $general_scoring_info . '</div>';
     }

     $reference = get_field('reference');
     if( $reference ) {
          $reference = '<div class="clb-single-dct-reference-wrapper"><p><strong>Reference:</strong> ' . $reference . '</p></div>';
     }

     $doi_link = get_field('doi_link');
     if( $doi_link ) {
          $doi_link = '<a href="' . esc_url($doi_link) . '" class="clb-dct-download-button" target="_blank" rel="noopener"><i class="fa-duotone fa-light fa-file-arrow-down"></i> Download Tool</a>';
     }

     // Taxonomies
     $purposes = get_the_term_list( $post_id, 'data_collection_purposes', 'Purpose: ', ', ' );
     $purposes = strip_tags( $purposes );
     if( $purposes ) {
          $purposes = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-bullseye-arrow"></i> ' . $purposes . '</div>';
     }

     $languages = get_the_term_list( $post_id, 'data_collection_languages', 'Language: ', ', ' );
     $languages = strip_tags( $languages );
     if( $languages ) {
          $languages = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-language"></i> ' . $languages . '</div>';
     }

     $populations = get_the_term_list( $post_id, 'data_collection_populations', 'Population: ', ', ' );
     $populations = strip_tags( $populations );
     if( $populations ) {
          $populations = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-users"></i> ' . $populations . '</div>';
     }

     $violence_modes = get_the_term_list( $post_id, 'data_collection_violence_modes', 'Violence Mode: ', ', ' );
     $violence_modes = strip_tags( $violence_modes );
     if( $violence_modes ) {
          $violence_modes = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-face-disappointed"></i> ' . $violence_modes . '</div>';
     }

     $time_frames = get_the_term_list( $post_id, 'data_collection_time_frame', 'Time Frame: ', ', ' );
     $time_frames = strip_tags( $time_frames );
     if( $time_frames ) {
          $time_frames = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-calendar-clock"></i> ' . $time_frames . '</div>';
     }

     $modes_of_admin = get_the_term_list( $post_id, 'data_collection_modes_of_admin', 'Mode of Admin: ', ', ' );
     $modes_of_admin = strip_tags( $modes_of_admin );
     if( $modes_of_admin ) {
          $modes_of_admin = '<div class="clb-single-dct-infoline"><i class="fa-duotone fa-light fa-clipboard-list"></i> ' . $modes_of_admin . '</div>';
     }

     // Output all together

     if( $doi_link ) { echo '<div class="clb-download-wrapper">' . $doi_link . '</div>'; }

     if( $brief_description ) { echo $brief_description; }

     echo '<div class="clb-dct-metabox-wrapper">' .
          $number_of_items .
          $measure_adaptations .
          $relationship .
          $application_setting .
          $general_scoring_info .
          $purposes .
          $languages .
          $populations .
          $violence_modes .
          $time_frames .
          $modes_of_admin .
     '</div>';

     if( $reference ) { echo $reference; }

     // Back to list link
     echo '<div class="clb-back-to-list">
               <a href="/data-collection-tools/"><i class="fa-duotone fa-light fa-arrow-left"></i> Back to Data Collection Tools</a>
          </div>';

}

genesis();
