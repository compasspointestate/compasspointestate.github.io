<?php 

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








