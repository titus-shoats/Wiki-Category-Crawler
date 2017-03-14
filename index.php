<?php 
require("curl/Curl.php");
set_time_limit(170);

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Wiki Category Crawler</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!---Styles-->

        <link href="./styles/main.css" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <!---Scripts-->
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="./js/app.js"></script>
        
<script type="text/javascript">
 
        $(document).ready(function(){
            
             WIKI.App.init();

        });

</script>
 
    </head>
    <body>
        
<div class="container">

                    <div class="row">
                      <div class="col-md-12">
                         <h3>WIKI API Category Crawler</h3>
                             
                                 <label for="form">Select a Category</label>
                                 <br/>
                                       
                           <form action="index.php" name="form" id="form" method="post" enctype="form-data">
                              <select name="categories" id="categories">
                            
                              <option class="categories"  selected="">Select a Category</option>
                              

                            </select>
                            <br/>
                            <br/>
                             <h3 id="searching"></h3>
                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                           </form>
                      </div>
                    </div>                 
 <?php


/****Create a curl object**/
$fetch_request = new Curl();

/****Declare a variable for a wiki extract**/
$wiki_cat_extract = array();

/****Declare a variable that decodes a curl response for a wiki title**/
$decode_title = array();

/****Create headers for our readability API**/
$readability_headers = [
   'X-Mashape-Key:IRE9x39MQImshJy7zJL21m4QxqORp1XfAXKjsnkzt0BFJfvfZS',
   'Content-Type:application/x-www-form-urlencoded',
   'Accept:application/json'
];
                        


 /****Check if POST categories is set**/
                  
if(!empty($_POST['categories'])){
     

            /****Sanitize Input**/

             $sanitize_input = filter_var($_POST['categories'],FILTER_SANITIZE_STRING); 
             ?>
             <h2>Category: <?php echo $sanitize_input; ?></h2>
             <?php  
   
             $decode_request= json_decode(
                                           $fetch_request->httpRequest('https://en.wikipedia.org/w/api.php',
                                           "action=query&list=categorymembers&cmlimit=10&cmtitle=Category:".$sanitize_input."&format=json",
                                           true,2)
                                           ,true
                                         );


             /****Iterate over nested decoded curl request**/

            $iterator_response = new RecursiveIteratorIterator(new RecursiveArrayIterator($decode_request));

            foreach($iterator_response as $key=>$value) {

              /****Check for WIKI Category API Errors **/
                   
                  if(strrpos($value, "invalidcategory") !== false or  strrpos($value, "invalidtitle") !== false or 
                             strrpos($value, "invalidtitleinfo") !== false  ){
                              die("The category name  or title you entered is not valid");
                   }

                     
                    /****If title exist, make curl request using category title**/

                 if($key ==="title"){
                 
                        $cat_title = urlencode($value);
                        $decode_title[]= json_decode($fetch_request->httpRequest('https://en.wikipedia.org/w/api.php','format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$cat_title.'',true,2),true);
                              
                  }
            }
     
            $iterator_response = new RecursiveIteratorIterator(new RecursiveArrayIterator($decode_title));
 
?>
<div class="row">

        
<?php

/****Loop over each category title and fetch articles**/

 foreach ($iterator_response as $key => $value) {
     

    if($key === "extract"){
       $articles = $value;
            
          /***
          Check for empty article , if empty, will show less articles
          */
           if(!empty($articles)){
                        

                      /***
                      Use Third Party Readability API to score Articles
                      */
                    $decode_readability = json_decode($fetch_request->httpRequest('https://ipeirotis-readability-metrics.p.mashape.com/getReadabilityMetrics','text='.$articles.'',true,2,$readability_headers),true);

                    array_push($decode_readability,$articles);
                    

                    /*****Sort Articles****/
                    array_multisort($decode_readability,SORT_DESC,SORT_NUMERIC);
                    extract($decode_readability);

                    
                    ?>
                        <div class="col-md-12 wiki-articles-container">

                                   
                                    <span><strong>SENTENCES:<?php echo $SENTENCES; ?></strong></span>
                                    <br/>
                                    <span><strong>WORDS:<?php echo $WORDS; ?></strong></span>
                                    <br/>
                                    <span><strong>SMOG:<?php echo $SMOG; ?></strong></span>
                                    <br/>
                                    <span><strong>COMPLEXWORDS:<?php echo $COMPLEXWORDS; ?></strong></span>
                                    <br/>
                                    <span><strong>COLEMAN_LIAU:<?php echo $COLEMAN_LIAU; ?></strong></span>
                                    <br/>
                                    <span><strong>FLESCH_READING:<?php echo $FLESCH_READING; ?></strong></span>
                                    <br/>
                                    <span><strong>ARI:<?php echo $ARI; ?></strong></span>
                                    <br/>
                                    <span><strong>SYLLABLES:<?php echo $SYLLABLES;?></strong></span>
                                    <br/>
                                    <span><strong>CHARACTERS:<?php echo $CHARACTERS;?></strong></span>
                                    <br/>
                                    <span><strong>SMOG_INDEX:<?php echo $SMOG_INDEX;?></strong></span>
                                    <br/>
                                    <span><strong>GUNNING_FOG:<?php echo $GUNNING_FOG;?></strong></span>
                                    <br/>
                                    <span><strong>FLESCH_KINCAID:<?php echo $FLESCH_KINCAID;?></strong></span>
                                    <br/>
                                        <p>
                                           <?php echo $decode_readability[0]; ?>
                                  </p>
                                  <div id="show_data"></div>
                             </div>
                    <?php
         
                  
                ?>
                 
                                
            <?php
           }


                                     
    }

 }

?>

</div>
<?php

      }
      
 ?>


          </div>

       </div>

</div>  
        
 </body>
</html>
