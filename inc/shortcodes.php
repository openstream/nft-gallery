<?php
/**
* NFT Gallery - shortcodes.php
*
* In this file,
* you will find all functions related to the shortcodes that are available on the plugin.
*
* @authors   Hendra Setiawan, Nick Weisser
* @version  1.3.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function nftgallery_function( $atts ){ 
        wp_enqueue_style( 'nftgallery' );
        wp_enqueue_style( 'justifiedGallery' );
        wp_enqueue_script( 'nftgallery' );
        wp_enqueue_script( 'justifiedGallery' );

        $args = array(
            'headers'     => array(
                'accept' => 'application/json',
                'x-api-key' => get_option('nftgallery-api')
            )
        );

        $type = get_option('nftgallery-type');
        $limit = get_option('nftgallery-limit');
        $id = get_option('nftgallery-id');
        $style = get_option('nftgallery-style');

        if ($type == 'owner') {
            $request = wp_remote_get('https://api.opensea.io/api/v2/chain/ethereum/account/'.$id.'/nfts?limit='.$limit, $args);
        } else {
            $request = wp_remote_get('https://api.opensea.io/api/v2/collection/'.$id.'/nfts?limit='.$limit, $args);
        }
        
        ob_start();
        $nfts = '';

        if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
            $nfts .= '<pre>No NFTs detected! Please check the wallet address or the collection slug.</pre>';
        } else {
            $body = wp_remote_retrieve_body( $request );

            $data = json_decode( $body );
            if( ! empty( $data ) ) {
                if($style == 'grid') {
                    wp_enqueue_style( 'flexbox' );
                    $nfts .= '<div class="row nftgallery">';
                    foreach( $data->nfts as $asset ) {
                        $image_headers = @get_headers($asset->image_url, 1);
                        if ($image_headers === false) { continue; }
                        if (isset($image_headers['Content-Type']) && strpos($image_headers['Content-Type'], 'video') !== false) {
                            continue; // Skip this iteration if the content type is a video
                        }
                        // IPFS images with parentheses wouldn't render ...
                        // Parse URL
                        $parsed_url = parse_url($asset->image_url);
                        if ($parsed_url === false) {
                            echo "<!-- Failed to parse URL: {$asset->image_url} -->\n";
                            continue;
                        }              
                        // Manually encode the path
                        $encoded_path = implode('/', array_map('rawurlencode', explode('/', $parsed_url['path'])));
                        // Rebuild the URL
                        $encoded_image_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $encoded_path;

                        // Replace ipfs.io with Cloudflare's IPFS gateway for caching
                        $image_url = str_replace('https://ipfs.io/ipfs/', 'https://cloudflare-ipfs.com/ipfs/', $encoded_image_url);

                        if($asset->name) { $title = $asset->name; } else { $title = '#'.$asset->identifier; }

                        $nfts .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 nftgallery-wrapper">';
                            $nfts .= '<div class="nft" data-url="'.$asset->opensea_url.'">';
                            $nfts .= '<div class="image" style="background-image: url(\''.$image_url.'\');"></div>';
                            $nfts .= '<div class="desc">
                                        <div class="collection">'.$asset->collection.'</div>
                                        <h2>'.$title.'</h2>
                                      </div>';
                            $nfts .= '</div>';
                        $nfts .= '</div>';
                    
                    }
                    if($type == 'collection'):

                        $nfts .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 os-button-wrapper"><a href="https://opensea.io/collection/'.$id.'" class="view-opensea" target="_blank">View '.$asset->name.' on OpenSea</a></div>';
                    
                    endif;
                    $nfts .= '</div>';
                } else if($style == 'photography') {
                    wp_enqueue_style( 'lightgallery' );        
                    wp_enqueue_style( 'lightgalleryzoom' );
                    wp_enqueue_style( 'lightgallerythumbnail' );
                    wp_enqueue_style( 'lightgallerytransition' );

                    wp_enqueue_script( 'lightgallery' );
                    wp_enqueue_script( 'lightgallerythumbnail' );
                    wp_enqueue_script( 'lightgalleryzoom' );      
                                           
                    $nfts .= '<div class="gallery-container nftgallery" id="lightgallery">';
                    $no = 1;
                    foreach( $data->nfts as $asset ) {
                        $image_headers = @get_headers($asset->image_url, 1);
                        if ($image_headers === false) { continue; }
                        if (isset($image_headers['Content-Type']) && strpos($image_headers['Content-Type'], 'video') !== false) {
                            continue; // Skip this iteration if the content type is a video
                        }
                        $basename = basename($asset->image_url);
                        if($asset->name) { $title = $asset->name; } else { $title = $asset->identifier; }
                        $title = strip_tags($title);
                        $title = preg_replace('#[^\w()/.%\-&]#'," ",$title);
                        
                        $nfts .= '<a data-src="'.$asset->image_url.'" data-download-url="false" class="gallery-item" data-sub-html=".caption'.$no.'">';
                        $nfts .= '<img class="img-fluid" src="'.$asset->image_url.'" />';
                        $nfts .= '<div class="caption caption'.$no.'"><p class="nft-title">'.$title.'</p><p>Collection: '.$asset->collection.'</strong></p><button class="openseaBtn" data-url="'.$asset->opensea_url.'">View on OpenSea</button></div>';
                        $nfts .= '</a>';
                        $no++;
                    }
                    $nfts .= '</div>';
                }
            }
        }
        
        echo wp_kses_post($nfts);
        return ob_get_clean(); 
}
add_shortcode('nftgallery', 'nftgallery_function');