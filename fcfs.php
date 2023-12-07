<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="design.css" />

    <title>First Come First Serve</title>
</head>

<body>
    <div class="container-fluid">
        <!--Navbar-->
        <nav class="navbar navbarr">
            <div class="container-fluid">
                <a href="#" class="navbar-brand page-title">First Come First Serve</a>
                <div class="d-flex">
                    <a href="main.html" class="nav-link me-4 navi">Home</a>
                    <a href="fcfs.php" class="nav-link me-4 navi">FCFS</a>
                    <a href="roundrobin.php" class="nav-link me-4 navi">Round Robin</a>
                    <a href="scan.php" class="nav-link me-4 navi">Scan</a>
                </div>
            </div>
        </nav>
    </div>

    <div class="container-fluid ">

        <div class="row">
            <!-- First Column - Input Form -->
            <div class="col-md-6">
                <form method="post">
                    <h1 class="title-margin"><b>First Come First Serve</b></h1>

                    <!-- Input Number of Processes -->
                    <label for="numProcesses" class="form-label"><b>Input no. of Processes [2-9]:</label></b>
                    <input type="number" class="form-control contF" id="numProcesses" name="numProcesses" min="2" max="9" value="<?php echo isset($_POST['numProcesses']) ? $_POST['numProcesses'] : ''; ?>" required>

                    <button class="btn btn-enter" name="enterButton" value="Enter">Enter</button>
                    <br><br>

                    <?php
                    // Retrieve the numProcess input
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $numProcesses = isset($_POST["numProcesses"]) ? (int)$_POST["numProcesses"] : 0;

                        echo "<b>Input Individual Burst Time</b><br><br>";
                        // Loop for every number of processes
                        for ($i = 0; $i < $numProcesses; $i++) {
                            $burstTimeValue = isset($_POST['burstTimes'][$i]) ? $_POST['burstTimes'][$i] : '';
                            echo "<label for='burstTime{$i}' class='form-label contF'><b>Burst Time</b> " . ($i + 1) . ": </label>";
                            echo "<input type='number' name='burstTimes[]' id='burstTime{$i}' required class='form-control contF' value='{$burstTimeValue}'>";
                        }
                        echo "<input type='submit' class='btn btn-enter' value='Calculate'>";
                    }
                    ?>
                </form>
            </div>

            <!-- Second Column - Results -->
            <div class="col-md-6 results-margin">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["burstTimes"])) {
                    $burstTimes = array_map('intval', $_POST["burstTimes"]);

                    function calculateTimes($numProcesses, $burstTimes)
                    {
                        $waitingTimes = $turnaroundTimes = array_fill(0, $numProcesses, 0);

                        // calculate waiting time and turnaround time per proc
                        for ($i = 1; $i < $numProcesses; $i++) {
                            $waitingTimes[$i] = $turnaroundTimes[$i - 1];
                            $turnaroundTimes[$i] = $waitingTimes[$i] + $burstTimes[$i];
                        }

                        // calculate average waiting time and average turnaround time
                        $averageWaitingTime = array_sum($waitingTimes) / $numProcesses;
                        $averageTurnaroundTime = $averageWaitingTime + (array_sum($burstTimes) / $numProcesses);

                        // display
                        echo "<table class='table'>
                        <tr>
                            <th class='table-width'>Process</th>
                            <th class='table-width'>Waiting Time</th>
                            <th class='table-width'>Turnaround Time</th>
                        </tr>";

                        for ($i = 0; $i < $numProcesses; $i++) {
                            echo "<tr>
                            <td class='table-width'>P" . ($i + 1) . "</td>
                            <td class='table-width'>" . $waitingTimes[$i] . "</td>
                            <td class='table-width'>" . $turnaroundTimes[$i] . "</td>
                          </tr>";
                        }

                        echo "</table>";

                        echo "<br><p><b>Average Waiting Time: </b> $averageWaitingTime</p>";
                        echo "<p><b>Average Turnaround Time: </b>$averageTurnaroundTime</p>";
                    }

                    calculateTimes($numProcesses, $burstTimes);

                    echo " <a href='fcfs.php' class='btn btn-enter'>Reset Values</a>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>