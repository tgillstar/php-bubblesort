<?php
/**
 * User: tgillstar
 * Date: 3/15/16
 * Time: 1:07 PM EST
 */

    // Create a session
    session_start();

    // Need the size of array, min number of range and max number of range
    $integersNeeded = 10;
    $min = 0;
    $max = 100;

    // Hold the current index for Step button
    $current = 0;

    // Shuffle Button - Create vector of random integers to start
    if (isset($_POST['shuffle'])){
        // Vector to hold random integers
        $selectedIntegers = array();
        // Create an array of numbers based on the range set
        $tempArray = range($min, $max);
        // Loop through the array the defined number of times to construct vector
        for($i = 0; $i <$integersNeeded; $i++)
        {
            // Generate a random number within the range
            $j = rand(1, count($tempArray))-1;
            // Insert random number into vector
            $selectedIntegers[] = $tempArray[$j];
            // Removed chosen random number from range so that it i not picked again
            array_splice($tempArray, $j, 1);
        }
        //Initialization of variables for html elements and session;
        $count = 0;
        $listOfIntegers = $selectedIntegers;
        $countSwapsDone = 0;
        $_SESSION['vector']=$selectedIntegers;
        $_SESSION['current']=0;
        $_SESSION['swapped'] = false;
        $_SESSION['countSwapsDone']=0;
        $_SESSION['disableBtn']=false;
    }

    // Step Button - Loop through vector and sort from largest to smallest
    if (isset($_POST['step'])) {
        for($step=0; $step < $integersNeeded; $step++) {
            $listOfIntegers = $_SESSION['vector'];
            // Check to see if the iteration is done before continuing
            if (!($_SESSION['current'] == $integersNeeded)) {
                // Counter for vertical vector list
                $count = 0;
                // Get the latest version of the vector array
                $listOfIntegers = $_SESSION['vector'];
                // Get the current index
                $current = $_SESSION['current'];
                //Get the items in order to display them on the page;
                $currentValue = $listOfIntegers[$current];
                $next = $current + 1;
                $nextValue = $listOfIntegers[$current + 1];

                // Check if next number is higher than current number
                // Swap the numbers if the current inter is smaller that the next integer
                if ($listOfIntegers[$current] < $listOfIntegers[$current + 1]) {
                    // Get a copy of the smaller integer
                    $temp = $listOfIntegers[$current];
                    // Highlight bar for the smaller integer on the graph
                    $selected = $temp;
                    // Switch the index of the larger integer with the location of the smaller integer in the vector
                    $listOfIntegers[$current] = $listOfIntegers[$current + 1];
                    $listOfIntegers[$current + 1] = $temp;
                    // Note that swap has occurred
                    $swapped = true;
                    $swapping = 'true';
                    // Display the swap status on the page display
                    $swapStatus = '$listOfIntegers[$current]<$listOfIntegers[$current+1], will swap';
                } else {
                    // No swap has taken place so note that on the page display and increment count of swaps for this iteration
                    $countSwapsDone = $_SESSION['countSwapsDone'] + 1;
                    $_SESSION['countSwapsDone']= $countSwapsDone;
                    $swapped = false;
                    $swapping = 'false';
                    $swapStatus = '$listOfIntegers[$current]<$listOfIntegers[$current+1], no swap';
                }
                // Initialization of session variables for next iteration;
                $_SESSION['current'] = $current + 1;
                $_SESSION['vector'] = $listOfIntegers;
                $_SESSION['swapped'] = $swapped;
                $selected = $temp;
            } else {
                // If the user has click through this iteration then update session variables and break out of current loop
                $_SESSION['current'] = 0;
                $_SESSION['countSwapsDone']= 0;
                break;
            }
            if (($_SESSION['countSwapsDone']<=9)){
                // Disable Step button since we are done.
                $_SESSION['disableBtn']=false;
                break;
            }else {
                $_SESSION['disableBtn']=true;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Bubblesort Simulation</title>
		<meta name="description" content="A simulation of the bubble sort algorithm using PHP">
		<meta name="author" content="Tiffany Gill">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/skeleton.css">
		<link rel="stylesheet" href="css/bubblesort.css">

	</head>
	<body>
    <header>
        <h1>Bubblesort Simulation</h1>
    </header>
    <section class="main">
            <div class="main-area">
                <form name="bubblesort" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="buttonBlock">
                        <input name="shuffle" id="btnShuffle" type="submit" value="Shuffle">
                        <input name="step" id="btnStep" type="submit" value="Step"
                            <?php if($_SESSION['disableBtn']==true){ echo 'disabled'; } else if ($_SESSION['disableBtn']=false){ echo ' '; } ?>>
                    </div>
                    <div class="detailsInfo">
                            <div class="infoBlock">Index: <input type="text" name="current" id="current" value = "<?php echo $current;?>" readonly></div>
                            <div class="infoBlock">Swap: <input type="text" name="swap" id="swap" value = "<?php echo $currentValue;?>" readonly></div>
                            <div class="infoBlock">Current (arr[index]): <input type="text" name="currentValue" id="currentValue" value = "<?php echo $currentValue;?>" readonly></div>
                            <div class="infoBlock">Swapped: <input type="text" name="swapped" id="swapped" value = "<?php echo $swapping;?>" readonly></div>
                            <div class="infoBlock">Next (arr[index+1]): <input type="text" name="nextValue" id="nextValue" value = "<?php echo $nextValue;?>" readonly></div>
                            <div class="infoBlock"><input type="text" name="swapStatus" id="swapStatus" value = "<?php echo $swapStatus;?>" readonly></div>
                    </div>
                </form>
            </div>
        </section>
        <section class="main">
            <div class="main-area">
                <div class="sidebar-left">
                    <?php foreach($listOfIntegers as $line) : ?>
                        <div class="stretchLine">
                                <div class="bar" id="<?php if ($selected == $line) echo 'barSelected'; ?>" style="width:<?php echo ((intval($line)/100)*100).'%'; ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="sidebar-right">
                    <table id="vectorList">
                        <thead>
                            <tr>
                                <th>i
                                    <div>i</div>
                                </th>
                                <th>Value
                                    <div>Value</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($listOfIntegers as $row) : ?>
                            <tr>
                                <td id="<?php if ($selected == $row) echo 'cellSelected'; ?>"><?php echo $count; ?></td>
                                <td id="<?php if ($selected == $row) echo 'cellSelected'; ?>"><?php echo $row; ?></td>
                            </tr>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
		<script src="http://code.jquery.com/jquery-1.12.1.min.js" integrity="sha256-I1nTg78tSrZev3kjvfdM5A5Ak/blglGzlaZANLPDl3I=" crossorigin="anonymous"></script>
    </body>
</html>