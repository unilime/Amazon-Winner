<?php

require_once __DIR__ . '/../vendor/autoload.php';

use duzun\hQuery;
use App\AmazonRecommendationHelper;

define('COUNT_OF_ITERATION', 5);

/**
 * Get filename from -f parametr (required)
 */
$shortopts = "f:";
$options = getopt( $shortopts );

if ( empty($options) ) {
    echo "Please provide filename with -f parameter\n";
    return;
}

// $requestList = file_get_contents('/var/opt/marakasdesign/storage/requests/test_request.txt');
$requestList = file_get_contents( $_SERVER['PWD'] . DIRECTORY_SEPARATOR . $options['f'] );

if ( $requestList === false ) {
    
    echo 'Couldn\'t open file ' . $_SERVER['PWD'] . DIRECTORY_SEPARATOR . $options['f'] . "\n";
    return;
}
/**
 * new line = new item
 */
$arrayRequestList = explode( PHP_EOL, $requestList );
/**
 * replace space with + (for Amazon URL compatibility)
 */
$arrayRequestList = array_map( function( $item ) {
    
    $item = trim($item);
    return str_replace( ' ', '+', $item );
}, $arrayRequestList );

/**
 * result array to transform to CSV
 */
$csvArray = [];

foreach ( $arrayRequestList as $requestItem ) {
    
    $spentIterations = 0;
    /**
     * Five attempts to get recommendation (sometimes not get page rightly for first attempt)
     */
    for ($i = COUNT_OF_ITERATION; $i > 0; $i--) {
        
        $doc = hQuery::fromUrl( 'https://www.amazon.com/s?k=' . $requestItem);

        /**
         * skip futher search and inform user about break url
         */
        if ( empty($doc) ) {
            
            echo "Couldn't resolve -" . 'https://www.amazon.com/s?k=' . $requestItem;
            return;
        }

        $recommendationPublisher = AmazonRecommendationHelper::getRecommendationArticlePublisher( $doc );
        
        $spentIterations++;
        
        if ( $recommendationPublisher !== null ) {
            break;
        }
    }
    
    $csvArray[$requestItem]['Publisher'] = $recommendationPublisher;

    if ( $recommendationPublisher !== null ) {
        $csvArray[$requestItem]['Article Name'] = AmazonRecommendationHelper::getRecommendationArticleName( $doc );
        $csvArray[$requestItem]['Publish Date'] = AmazonRecommendationHelper::getRecommendationArticlePublishDate( $doc );
        $csvArray[$requestItem]['Article URL'] = AmazonRecommendationHelper::getRecommendationArticleURL( $doc );
        $csvArray[$requestItem]['Scraping date'] = date('Y-d-m');

        echo $requestItem . " => recommendation was found (Spent iterations: " . $spentIterations . ")\n";
    } else {
        echo $requestItem . " => WARNING: recommendation was not found (Spent iterations: " . $spentIterations . ")\n";
    }
}

if ( AmazonRecommendationHelper::saveCSV( __DIR__ . '/../storage/reports/' .'KEYWORDWINNER_' . date('Y_d_m') . '.csv', $csvArray ) > 0 ) {
    echo "\nReport was successfully stored to storage/reports\n";
} else {
    echo "\nError occurred during report saving\n";
};