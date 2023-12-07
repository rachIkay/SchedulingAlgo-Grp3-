<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Round Robin Scheduler</title>
</head>

<body>
    <h1>Round Robin Scheduler</h1>
    <?php
    // Function to update the queue
    function queueUpdation(&$queue, $timer, $arrival, $n, &$maxProccessIndex)
    {
        $zeroIndex = array_search(0, $queue);
        if ($zeroIndex === false) {
            return;
        }
        $queue[$zeroIndex] = $maxProccessIndex + 1;
    }

    // Function to check for new arrival and update the queue
    function checkNewArrival($timer, $arrival, $n, &$maxProccessIndex, &$queue)
    {
        if ($timer <= end($arrival)) {
            $newArrival = false;
            for ($j = ($maxProccessIndex + 1); $j < $n; $j++) {
                if ($arrival[$j] <= $timer) {
                    if ($maxProccessIndex < $j) {
                        $maxProccessIndex = $j;
                        $newArrival = true;
                    }
                }
            }
            if ($newArrival) {
                queueUpdation($queue, $timer, $arrival, $n, $maxProccessIndex);
            }
        }
    }

    // Function to maintain the queue
    function queueMaintenance(&$queue, $n)
    {
        for ($i = 0; $i < $n - 1 && isset($queue[$i + 1]) && $queue[$i + 1] != 0; $i++) {
            $temp = $queue[$i];
            $queue[$i] = $queue[$i + 1];
            $queue[$i + 1] = $temp;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $n = $_POST['n'];
        $tq = $_POST['tq'];
        $timer = 0;
        $maxProccessIndex = 0;
        $avgWait = 0;
        $avgTT = 0;

        $arrival = array_map('intval', preg_split('/\s+/', trim($_POST['arrival'])));
        $burst = array_map('intval', preg_split('/\s+/', trim($_POST['burst'])));

        $temp_burst = $burst;
        $wait = $turn = $queue = array_fill(0, $n, 0);
        $complete = array_fill(0, $n, false);

        while ($timer < $arrival[0]) {
            $timer++;
        }
        $queue[0] = 1;

        while (!empty($temp_burst) && array_sum($temp_burst) > 0) {
            $value = $queue[0];
            if (isset($complete[$value - 1]) && $complete[$value - 1] == false) {
                $ctr = 0;
                while ($ctr < $tq && isset($temp_burst[$value - 1]) && $temp_burst[$value - 1] > 0) {
                    $temp_burst[$value - 1] -= 1;
                    $timer += 1;
                    $ctr++;

                    checkNewArrival($timer, $arrival, $n, $maxProccessIndex, $queue);
                }

                if (isset($temp_burst[$value - 1]) && $temp_burst[$value - 1] == 0 && !$complete[$value - 1]) {
                    $turn[$value - 1] = $timer;
                    $complete[$value - 1] = true;
                }

                $idle = true;
                if (isset($queue[$n - 1]) && $queue[$n - 1] === 0) {
                    foreach ($queue as $k => $v) {
                        if (!$complete[$v - 1]) {
                            $idle = false;
                        }
                    }
                } else {
                    $idle = false;
                }

                if ($idle) {
                    $timer++;
                    checkNewArrival($timer, $arrival, $n, $maxProccessIndex, $queue);
                }

                queueMaintenance($queue, $n);
            }
        }

        for ($i = 0; $i < count($arrival); $i++) {
            $turn[$i] = $turn[$i] - $arrival[$i];
            $wait[$i] = $turn[$i] - $burst[$i];
        }

        echo "<h2>Result</h2>";
        echo "<table border='1'>
            <tr>
                <th>Program No.</th>
                <th>Arrival Time</th>
                <th>Burst Time</th>
                <th>Wait Time</th>
                <th>Turnaround Time</th>
            </tr>";

        for ($i = 0; $i < count($arrival); $i++) {
            echo "<tr>
                <td>" . ($i + 1) . "</td>
                <td>" . $arrival[$i] . "</td>
                <td>" . $burst[$i] . "</td>
                <td>" . $wait[$i] . "</td>
                <td>" . $turn[$i] . "</td>
            </tr>";
        }

        echo "</table>";

        $avgWait = array_sum($wait);
        $avgTT = array_sum($turn);

        echo "<h3>Average wait time: " . ($avgWait / $n) . "</h3>";
        if ($n != 0) {
            echo "<h3>Average Turnaround Time: " . ($avgTT / $n) . "</h3>";
        } else {
            echo "<h3>No processes to calculate averages.</h3>";
        }
    }
    ?>

    <h2>Enter Process Information</h2>
    <form method="post">
        <label for="tq">Enter the time quantum:</label>
        <input type="number" name="tq" required>
        <br>
        <label for="n">Enter the number of processes:</label>
        <input type="number" name="n" required>
        <br>
        <label for="arrival">Enter the arrival time of the processes (space-separated):</label>
        <input type="text" name="arrival" required>
        <br>
        <label for="burst">Enter the burst time of the processes (space-separated):</label>
        <input type="text" name="burst" required>
        <br>
        <input type="submit" value="Run Scheduler">
    </form>
</body>

</html>