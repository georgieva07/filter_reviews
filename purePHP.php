<?php

function sortByRating($reviews, $flag){


    //asc => flag=false
    if(!$flag){
        for($i=0; $i<count($reviews)-1; $i++){
        
            for($j=0; $j<count($reviews)-$i-1; $j++){
                
                if($reviews[$j]['rating'] > $reviews[$j+1]['rating']){
                    
                    $temp=$reviews[$j];
                    $reviews[$j]=$reviews[$j+1];
                    $reviews[$j+1]=$temp;
                }
            }
        }  
    }
    //desc => flag=true
    else{

        for($i=0; $i<count($reviews)-1; $i++){
        
            for($j=0; $j<count($reviews)-$i-1; $j++){
                
                if($reviews[$j]['rating'] < $reviews[$j+1]['rating']){
                    
                    $temp=$reviews[$j];
                    $reviews[$j]=$reviews[$j+1];
                    $reviews[$j+1]=$temp;
                }
            }
        }
    }
    

    return $reviews;
}

function sortByDate($reviews, $flag){


    //asc => flag=false
    if(!$flag){
        for($i=0; $i<count($reviews)-1; $i++){
        
            for($j=0; $j<count($reviews)-$i-1; $j++){
                
                if($reviews[$j]['reviewCreatedOnDate'] > $reviews[$j+1]['reviewCreatedOnDate'] && $reviews[$j]['rating'] == $reviews[$j+1]['rating']){
                    
                    $temp=$reviews[$j];
                    $reviews[$j]=$reviews[$j+1];
                    $reviews[$j+1]=$temp;
                }
            }
        }  
    }
    //desc => flag=true
    else{

        for($i=0; $i<count($reviews)-1; $i++){
        
            for($j=0; $j<count($reviews)-$i-1; $j++){
                
                if($reviews[$j]['reviewCreatedOnDate'] < $reviews[$j+1]['reviewCreatedOnDate'] && $reviews[$j]['rating'] == $reviews[$j+1]['rating']){
                    
                    $temp=$reviews[$j];
                    $reviews[$j]=$reviews[$j+1];
                    $reviews[$j+1]=$temp;
                }
            }
        }
    }
    

    return $reviews;
}

function minimumRating($reviews, $rating){
    $filteredArray=array();
    $j=0;
    for($i=0; $i<count($reviews); $i++){
        if($reviews[$i]['rating']>=$rating){
            $filteredArray[$j]=$reviews[$i];
            $j++;
        }
    }

    return $filteredArray;
}

//read reviews
$reviews = file_get_contents("json/reviews.json");
$reviews = json_decode($reviews, true);

//get sorting preferences
$text=1;
$rating = 1;
$date = 1; 
$minimal = 1;

#remove reviews whose rating is lower than the minimal
$minRatingReviews = minimumRating($reviews, $minimal);

//split the reviews in 2 arrays depending on whether they have review text or not
$prioritized=array(array(), array());
$j=0;
$k=0;
if($text == 1){

    for($i=0; $i<count($minRatingReviews); $i++){
        if($minRatingReviews[$i]['reviewText'] == ""){
            $prioritized[1][$k]=$minRatingReviews[$i];
            $k++;
        }
        else{
            $prioritized[0][$j]=$minRatingReviews[$i];
            $j++;
        }
    }
}
else{
    
    $prioritized[0]=$minRatingReviews;
    $prioritized[1]=null;
}

//sort the reviews by rating
if($rating==0){
    $prioritized[0]=sortByRating($prioritized[0], false);
    if($text==1){
        $prioritized[1]=sortByRating($prioritized[1], false);
    }
}
else{
    $prioritized[0]=sortByRating($prioritized[0], true);
    if($text==1){
        $prioritized[1]=sortByRating($prioritized[1], true);
    }
}

//sort the reviews by date
if($date==0){
    $prioritized[0]=sortByDate($prioritized[0], false);
    if($text==1){
        $prioritized[1]=sortByDate($prioritized[1], false);
    }
}
else{
    $prioritized[0]=sortByDate($prioritized[0], true);
    if($text==1){
        $prioritized[1]=sortByDate($prioritized[1], true);
    }
}

//print
foreach($prioritized[0] as $review){
    echo $review['rating']." ".$review['reviewText']." ".$review['reviewCreatedOnDate']."<br>";
}

if($text==1){
   foreach($prioritized[1] as $review){
       echo $review['rating']." ".$review['reviewText']." ".$review['reviewCreatedOnDate']."<br>";
   }
}