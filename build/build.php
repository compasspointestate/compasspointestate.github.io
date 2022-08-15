<?php 
/*




*/




/* Content section */

# setup
$start_tag = "<!--content_start-->";
$end_tag = "<!--content_end-->";

# read in json 
$build_data = json_decode( file_get_contents("build.json") );


# read in the top base
$top_base = file_get_contents("top.html");

# read in the bottom base
$bottom_base = file_get_contents("bottom.html");

foreach($build_data AS $this_build => $this_data) {

    echo "processing " . $this_build . " \n";

    print_r($this_data);

    $file_name = "../" . $this_build . ".html";
    
    # fix up the top
    $this_top = $top_base;
    $this_top = str_replace("TITLE_HERE", $this_data->title, $this_top);
    $this_top = str_replace("DESCRIPTION_HERE", $this_data->description, $this_top);

    # fix up the bottom
    $this_bottom = $bottom_base;

    # read in each file 
    $file_content = file_get_contents($file_name);

    # get the start tag position
    $start_of_content = strpos($file_content, $start_tag);

    # get the end tag position
    $end_of_content = strpos($file_content, $end_tag) + strlen($end_tag);

    # get the content bit
    $body_content = substr($file_content, $start_of_content, $end_of_content - $start_of_content);

    echo "body_content: " . $body_content . "\n";

    # add on the top and bottom 
    $new_file_content = $this_top . "\n" . $body_content . "\n" . $this_bottom;

    file_put_contents($file_name, $new_file_content);

}




/* Image section */

$small_width = 200;
$small_height = 200;
$medium_width = 400;
$medium_height = 400;
$large_width = 800;
$large_height = 800;

foreach ( glob("../assets/images/*_small.jpg" ) as $filename) {
    echo "delete " . $filename . "\n";
    unlink($filename);
}
foreach ( glob("../assets/images/*_medium.jpg" ) as $filename) {
    echo "delete " . $filename . "\n";
    unlink($filename);
}
foreach ( glob("../assets/images/*_large.jpg" ) as $filename) {
    echo "delete " . $filename . "\n";
    unlink($filename);
}



foreach ( glob("../design/original_photos/*.jpg" ) as $full_filename) {
    echo $full_filename . "\n";
    $filename = basename($full_filename, ".jpg");
    $small_filename = "../assets/images/" . $filename . "_small.jpg";
    $medium_filename = "../assets/images/" . $filename . "_medium.jpg";
    $large_filename = "../assets/images/" . $filename . "_large.jpg";

    # read in the image
    $image_data = imagecreatefromjpeg($full_filename);

    # resize it, small 
    $new_image_object = resize_image($image_data, $small_width, $small_height);

    # save it out
    imagejpeg($new_image_object, $small_filename);

    # resize it, medium 
    $new_image_object = resize_image($image_data, $medium_width, $medium_height);

    # save it out
    imagejpeg($new_image_object, $medium_filename);

    # resize it, large 
    $new_image_object = resize_image($image_data, $large_width, $large_height);

    # save it out
    imagejpeg($new_image_object, $large_filename);


}






function resize_image( $image_obj, $max_width, $max_height ) {
    $input_width = imagesx($image_obj);
    $input_height = imagesy($image_obj);

    echo "max size: " . $max_width . "x" .  $max_height . "\n";
    $max_ratio = $max_width / $max_height;
    echo "max_ratio: " . $max_ratio . "\n";

    echo "input size: " . $input_width . "x" .  $input_height . "\n";      
    $input_ratio = $input_width / $input_height;
    echo "input_ratio: " . $input_ratio . "\n"; 
    
    if($input_ratio > $max_ratio) {
        # wider than max, so width is the limiter
        $height = round($max_width / $input_ratio);
        $image_obj = imagescale($image_obj, $max_width, $height);
        echo "wide image: " . $max_width . "x" . $height . "\n";
    } else {
        # taller than max, so height is the limiter
        $width = round($max_height * $input_ratio);
        $image_obj = imagescale($image_obj, $width, $max_height);
        echo "tall image: " . $width . "x" . $max_height . "\n";
    }        
    return $image_obj;
} 