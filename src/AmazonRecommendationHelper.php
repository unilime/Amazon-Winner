<?php

namespace App;

use duzun\hQuery;

class AmazonRecommendationHelper
{
    /**
     * @param hQuery $doc
     * 
     * @return string|null
     */
    public static function getRecommendationArticlePublisher( hQuery $doc ): ?string
    {
        $headerLinks = $doc->find( '.s-shopping-adviser-heading a.a-link-normal' );

        if ( isset( $headerLinks ) ) {

            foreach ( $headerLinks as $link ) {
                
                $publisherName = trim( $link->text() );
        
                if ( $publisherName !== 'Onsite Associates Program' ) {
                    return $publisherName;
                }
            }
        }
        
        return null;
    }
    /**
     * @param hQuery $doc
     * 
     * @return string|null
     */
    public static function getRecommendationArticleName( hQuery $doc ): ?string
    {
        $articleName = $doc->find( '.s-widget .a-carousel-row-inner .a-size-large' );

        if ( isset( $articleName ) ) {
            
            return trim( $articleName->text() );
        }
        
        return null;
    }
    /**
     * @param hQuery $doc
     * 
     * @return string|null
     */
    public static function getRecommendationArticlePublishDate( hQuery $doc ): ?string
    {
        $publishDate = $doc->find( '.s-widget  .a-carousel-row-inner .a-section.a-spacing-medium .a-color-secondary' );

        if ( isset( $publishDate ) ) {
            
            preg_match( '/\w+ \d+, \d+ /', $publishDate->text(), $result );
            return trim($result[0]);
        }
        
        return null;
    }
    /**
     * @param hQuery $doc
     * 
     * @return string|null
     */
    public static function getRecommendationArticleURL( hQuery $doc ): ?string
    {
        $articleURL = $doc->find( '.s-widget  .a-carousel-row-inner .a-section.a-spacing-medium .a-row.a-spacing-medium .a-link-normal' );

        if ( isset( $articleURL ) ) {
            
            return $articleURL->attr('href');
        }
        
        return null;
    }
    /**
     * @param string $filename
     * @param array $itemsList
     * 
     * @return int|null
     */
    public static function saveCSV( string $filename, array $itemsList ): ?int
    {
        $accumulator = 0;
        $fp = fopen( $filename, 'w' );

        foreach ( $itemsList as $item) {
            
            if ( $item['Publisher'] === null ) {
                $accumulator += fputcsv($fp, [ 
                    'no_recommendation'
                ]);
            } else {

                $accumulator += fputcsv($fp, [ 
                    $item['Publisher'],
                    $item['Article Name'],
                    $item['Publish Date'],
                    $item['Article URL'],
                    $item['Scraping date']
                ]);
            }
        }

        fclose($fp);

        return $accumulator;
    }
}