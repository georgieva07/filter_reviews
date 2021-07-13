<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:th="http://www.thymeleaf.org">
<head>
    <meta charset="UTF-8">
    <title>Filter reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>
<body>
<div class="container">
    <div class="h2">Filter reviews
    </div>
</div>
<br>
<br>
<form method="POST" action="filter_reviews.php">
    <div class="container">
                <div class="card border-light mb-3" >
                    <div class="row g-0">
                        <div class="col-md-4" >
                            <p>Order by rating:</p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-4">
                            <select  class="btn btn-default dropdown-toggle" id="orderByRating" name="orderByRating" style="width: 100%">
                                <option value="1">Highest First</option>
                                <option value="0">Lowest First</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row g-0">
                        <div class="col-md-4" >
                            <p>Minimum rating:</p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-4">
                            <select  class="btn btn-default dropdown-toggle" id="minimumRating" name="minimumRating" style="width: 100%">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row g-0">
                        <div class="col-md-4" >
                            <p>Order by date:</p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-4">
                            <select  class="btn btn-default dropdown-toggle" id="orderByDate" name="orderByDate" style="width: 100%">
                                <option value="1">Newest First</option>
                                <option value="0">Oldest First</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row g-0">
                        <div class="col-md-4" >
                            <p>Prioritize by text:</p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-4">
                            <select  class="btn btn-default dropdown-toggle" id="prioritizeByText" name="prioritizeByText" style="width: 100%">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row g-0">
                        <div class="col-md-10">
                            <input  id="filter" type="submit" name="filter" class="btn btn-primary btn-sm">Filter</button>
                        </div>
                    </div>
                </div>
    </div>
</form>
<br>
<br>

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
$rating=1;
$minimal=1;
$date=1;

if(isset($_POST["prioritizeByText"])){
    $text = (int)$_POST["prioritizeByText"];
}
if(isset($_POST["orderByRating"])){
    $rating = (int)$_POST["orderByRating"];
}
if(isset($_POST["minimumRating"])){
    $minimal = (int)$_POST["minimumRating"];
}
if(isset($_POST["orderByDate"])){
    $date = (int)$_POST["orderByDate"]; 
}

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
    //if the reviews are prioritized by text, sort the ones without text as well
    if($text==1){
        $prioritized[1]=sortByRating($prioritized[1], false);
    }
}
else{
    $prioritized[0]=sortByRating($prioritized[0], true);
    //if the reviews are prioritized by text, sort the ones without text as well
    if($text==1){
        $prioritized[1]=sortByRating($prioritized[1], true);
    }
}

//sort the reviews by date
if($date==0){
    $prioritized[0]=sortByDate($prioritized[0], false);
    //if the reviews are prioritized by text, sort the ones without text as well
    if($text==1){
        $prioritized[1]=sortByDate($prioritized[1], false);
    }
}
else{
    $prioritized[0]=sortByDate($prioritized[0], true);
    //if the reviews are prioritized by text, sort the ones without text as well
    if($text==1){
        $prioritized[1]=sortByDate($prioritized[1], true);
    }
}
?>

<div class="container">
    <div class="h2">Reviews
    </div>
    <br>
    <br>
    <table class="table">
        <thead>
            <tr>
                <td>Rating</td>
                <td>Comment</td>
                <td>Created by</td>
                <td>Created</td>
                <td>Date and Time</td>
            </tr>
        </thead>
        <tbody>
            
            <?php

            //print
                
            foreach($prioritized[0] as $review){
                echo "<tr>
                <td>".$review['rating']."</td>
                <td>".$review['reviewText']."</td>
                <td>".$review['reviewerName']."</td>
                <td>".$review['reviewCreatedOn']."</td>
                <td>".$review['reviewCreatedOnDate']."</td>
                </tr>";
            }
            //if the reviews are prioritized by text, print the ones without text as well
            if($text==1){
                foreach($prioritized[1] as $review){
                    echo "<tr>
                <td>".$review['rating']."</td>
                <td>".$review['reviewText']."</td>
                <td>".$review['reviewerName']."</td>
                <td>".$review['reviewCreatedOn']."</td>
                <td>".$review['reviewCreatedOnDate']."</td>
                </tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>