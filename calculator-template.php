<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



function calculateSIP($monthly_sip, $years, $annual_return) {
    $months = $years * 12;
    $monthly_rate = $annual_return / (12 * 100);
    $amount_invested = $monthly_sip * $months;
    $maturity_value = $monthly_sip * (pow(1 + $monthly_rate, $months) - 1) * (1 + $monthly_rate) / $monthly_rate;
    return array($amount_invested, $maturity_value);
}

function calculateLumpsum($one_time_amount, $years, $annual_return) {
    $amount_invested = $one_time_amount;
    $maturity_value = $one_time_amount * pow(1 + $annual_return / 100, $years);
    return array($amount_invested, $maturity_value);
}

function calculateSWP($initial_investment, $monthly_withdrawal, $years, $annual_return) {
    $months = $years * 12;
    $monthly_rate = $annual_return / (12 * 100);
    $balance = $initial_investment;
    $total_withdrawal = 0;
    $monthly_data = array();

    for ($i = 1; $i <= $months; $i++) {
        
        $balance -= $monthly_withdrawal;
	$interest_earned = $balance * $monthly_rate;
	$balance += $interest_earned;	
        $total_withdrawal += $monthly_withdrawal;

        $monthly_data[] = array(
            'month' => $i,
            'balance_begin' => $balance + $monthly_withdrawal - $interest_earned,
            'interest_earned' => $interest_earned,
            'withdrawal' => $monthly_withdrawal,
            'balance_end' => $balance
        );
    }

    return array($initial_investment, $total_withdrawal, $balance, $monthly_data);
}

function calculateGoal($present_value, $years, $inflation, $annual_return) {
    $future_value = $present_value * pow(1 + $inflation / 100, $years);
    $monthly_sip = ($future_value * ($annual_return / 12 / 100)) / (pow(1 + $annual_return / 12 / 100, $years * 12) - 1);
    return array($future_value, $monthly_sip);
}

// Initialize variables to store form inputs
$sip_inputs = array('monthly_sip' => '', 'sip_years' => '', 'sip_annual_return' => '');
$lumpsum_inputs = array('one_time_amount' => '', 'lumpsum_years' => '', 'lumpsum_annual_return' => '');
$swp_inputs = array('initial_investment' => '', 'monthly_withdrawal' => '', 'swp_years' => '', 'swp_annual_return' => '');
$goal_inputs = array('present_value' => '', 'goal_years' => '', 'inflation' => '', 'goal_annual_return' => '');

// Store form inputs if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['calculate_sip'])) {
        $sip_inputs = array_intersect_key($_POST, $sip_inputs);
    } elseif (isset($_POST['calculate_lumpsum'])) {
        $lumpsum_inputs = array_intersect_key($_POST, $lumpsum_inputs);
    } elseif (isset($_POST['calculate_swp'])) {
        $swp_inputs = array_intersect_key($_POST, $swp_inputs);
    } elseif (isset($_POST['calculate_goal'])) {
        $goal_inputs = array_intersect_key($_POST, $goal_inputs);
    }
}

?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Calculator</title>
    <script src="<?php echo plugins_url('calculator-script.js', __FILE__); ?>"></script>
    <link rel="stylesheet" href="<?php echo plugins_url('calculator-styles.css', __FILE__); ?>">


    <div class="calculator">
        <div class="tabs">
            <div class="tab active" data-tab="sip">SIP</div>
            <div class="tab" data-tab="lumpsum">Lumpsum</div>
            <div class="tab" data-tab="swp">SWP (Pension)</div>
            <div class="tab" data-tab="goal">Goal</div>
        </div>
        <div class="calculator-content">
            <div class="input-section">
                <form method="post" id="sipForm" class="calculator-form active">
		    <?php wp_nonce_field('cic_calculator_nonce'); ?>	
                    <label for="monthly_sip">Monthly SIP Amount:</label>
                    <input type="number" id="monthly_sip" name="monthly_sip" value="<?php echo $sip_inputs['monthly_sip']; ?>" required>

                    <label for="sip_years">Number of Years:</label>
                    <input type="number" id="sip_years" name="sip_years" value="<?php echo $sip_inputs['sip_years']; ?>" required>

                    <label for="sip_annual_return">Annual Return %:</label>
                    <input type="number" id="sip_annual_return" name="sip_annual_return" step="0.1" value="<?php echo $sip_inputs['sip_annual_return']; ?>" required>

                    <input type="submit" name="calculate_sip" value="Calculate">
                </form>

                <form method="post" id="lumpsumForm" class="calculator-form">
                    <?php wp_nonce_field('cic_calculator_nonce'); ?>
                    <label for="one_time_amount">One Time Amount:</label>
                    <input type="number" id="one_time_amount" name="one_time_amount" value="<?php echo $lumpsum_inputs['one_time_amount']; ?>" required>

                    <label for="lumpsum_years">Number of Years:</label>
                    <input type="number" id="lumpsum_years" name="lumpsum_years" value="<?php echo $lumpsum_inputs['lumpsum_years']; ?>" required>

                    <label for="lumpsum_annual_return">Annual Return %:</label>
                    <input type="number" id="lumpsum_annual_return" name="lumpsum_annual_return" step="0.1" value="<?php echo $lumpsum_inputs['lumpsum_annual_return']; ?>" required>

                    <input type="submit" name="calculate_lumpsum" value="Calculate">
                </form>

                <form method="post" id="swpForm" class="calculator-form">
                    <?php wp_nonce_field('cic_calculator_nonce'); ?>
                    <label for="initial_investment">Initial Investment:</label>
                    <input type="number" id="initial_investment" name="initial_investment" value="<?php echo $swp_inputs['initial_investment']; ?>" required>

                    <label for="monthly_withdrawal">Monthly Pension/SWP Amount:</label>
                    <input type="number" id="monthly_withdrawal" name="monthly_withdrawal" value="<?php echo $swp_inputs['monthly_withdrawal']; ?>" required>

                    <label for="swp_years">Number of Years:</label>
                    <input type="number" id="swp_years" name="swp_years" value="<?php echo $swp_inputs['swp_years']; ?>" required>

                    <label for="swp_annual_return">Annual Return %:</label>
                    <input type="number" id="swp_annual_return" name="swp_annual_return" step="0.1" value="<?php echo $swp_inputs['swp_annual_return']; ?>" required>

                    <input type="submit" name="calculate_swp" value="Calculate">
                </form>

                <form method="post" id="goalForm" class="calculator-form">
                    <?php wp_nonce_field('cic_calculator_nonce'); ?>
                    <label for="present_value">Present Value:</label>
                    <input type="number" id="present_value" name="present_value" value="<?php echo $goal_inputs['present_value']; ?>" required>

                    <label for="goal_years">No. of years:</label>
                    <input type="number" id="goal_years" name="goal_years" value="<?php echo $goal_inputs['goal_years']; ?>" required>

                    <label for="inflation">Inflation %:</label>
                    <input type="number" id="inflation" name="inflation" step="0.1" value="<?php echo $goal_inputs['inflation']; ?>" required>

                    <label for="goal_annual_return">Annual %:</label>
                    <input type="number" id="goal_annual_return" name="goal_annual_return" step="0.1" value="<?php echo $goal_inputs['goal_annual_return']; ?>" required>

                    <input type="submit" name="calculate_goal" value="Calculate">
                </form>

                <?php
                if (isset($_POST['calculate_sip']) && check_admin_referer('cic_calculator_nonce')) {
                    $monthly_sip = $_POST["monthly_sip"];
                    $years = $_POST["sip_years"];
                    $annual_return = $_POST["sip_annual_return"];

                    list($amount_invested, $maturity_value) = calculateSIP($monthly_sip, $years, $annual_return);

                    echo "<div class='result'>";
                    echo "<p>Amount Invested: Rs. " . number_format($amount_invested, 2) . "</p>";
                    echo "<p>Maturity Value: Rs. " . number_format($maturity_value, 2) . "</p>";
                    echo "</div>";

                    echo "<table>";
                    echo "<tr><th>Year</th><th>SIP Amt / Month</th><th>Total Invested Amt</th><th>Interest Amt / Year</th><th>Maturity Value</th></tr>";

                    $total_invested = 0;
                    $current_value = 0;

                    for ($i = 1; $i <= $years; $i++) {
                        $total_invested += $monthly_sip * 12;
                        $current_value = ($current_value + $monthly_sip * 12) * (1 + $annual_return / 100);
                        $interest = $current_value - $total_invested;

                        echo "<tr>";
                        echo "<td>Year " . $i . "</td>";
                        echo "<td>" . number_format($monthly_sip, 2) . "</td>";
                        echo "<td>" . number_format($total_invested, 2) . "</td>";
                        echo "<td>" . number_format($interest, 2) . "</td>";
                        echo "<td>" . number_format($current_value, 2) . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";

                    $total_growth = $maturity_value - $amount_invested;
                    
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('investmentChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Total SIP Amount Invested', 'Total Growth'],
                                    datasets: [{
                                        data: [$amount_invested, $total_growth],
                                        backgroundColor: ['#00B2FF', '#BFFF00']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        });
                    </script>";
                }
                elseif (isset($_POST['calculate_lumpsum']) && check_admin_referer('cic_calculator_nonce')) {
                    $one_time_amount = $_POST["one_time_amount"];
                    $years = $_POST["lumpsum_years"];
                    $annual_return = $_POST["lumpsum_annual_return"];

                    list($amount_invested, $maturity_value) = calculateLumpsum($one_time_amount,

 $years, $annual_return);

                    echo "<div class='result'>";
                    echo "<p>Amount Invested: Rs. " . number_format($amount_invested, 2) . "</p>";
                    echo "<p>Maturity Value: Rs. " . number_format($maturity_value, 2) . "</p>";
                    echo "</div>";

                    echo "<table>";
                    echo "<tr><th>Year</th><th>Amount Invested</th><th>Interest</th><th>Maturity Value</th></tr>";

                    $current_value = $amount_invested;

                    for ($i = 1; $i <= $years; $i++) {
                        $new_value = $current_value * (1 + $annual_return / 100);
                        $interest = $new_value - $current_value;

                        echo "<tr>";
                        echo "<td>Year " . $i . "</td>";
                        echo "<td>" . ($i == 1 ? number_format($amount_invested, 2) : "One Time") . "</td>";
                        echo "<td>" . number_format($interest, 2) . "</td>";
                        echo "<td>" . number_format($new_value, 2) . "</td>";
                        echo "</tr>";

                        $current_value = $new_value;
                    }

                    echo "</table>";

                    $total_growth = $maturity_value - $amount_invested;
                    
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('investmentChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Amount Invested', 'Total Growth'],
                                    datasets: [{
                                        data: [$amount_invested, $total_growth],
                                        backgroundColor: ['#00B2FF', '#BFFF00']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        });
                    </script>";
                }
                elseif (isset($_POST['calculate_swp']) && check_admin_referer('cic_calculator_nonce')) {
                    $initial_investment = $_POST["initial_investment"];
                    $monthly_withdrawal = $_POST["monthly_withdrawal"];
                    $years = $_POST["swp_years"];
                    $annual_return = $_POST["swp_annual_return"];

                    list($amount_invested, $total_withdrawal, $balance, $monthly_data) = calculateSWP($initial_investment, $monthly_withdrawal, $years, $annual_return);

                    echo "<div class='result'>";
                    echo "<p>Total Pension Drawn: Rs. " . number_format($total_withdrawal, 2) . "</p>";
                    echo "<p>Balance Worth: Rs. " . number_format($balance, 2) . "</p>";
                    echo "<p>Grand Total: Rs. " . number_format($total_withdrawal + $balance, 2) . "</p>";
                    echo "</div>";

                    echo "<table>";
                    echo "<tr><th>Month</th><th>Balance at Begin</th><th>Interest Earned</th><th>Withdrawal</th><th>Balance at End</th></tr>";

                    foreach ($monthly_data as $data) {
                        echo "<tr>";
                        echo "<td>" . $data['month'] . "</td>";
                        echo "<td>" . number_format($data['balance_begin'], 2) . "</td>";
                        echo "<td>" . number_format($data['interest_earned'], 2) . "</td>";
                        echo "<td>" . number_format($data['withdrawal'], 2) . "</td>";
                        echo "<td>" . number_format($data['balance_end'], 2) . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";

                    $profit = $total_withdrawal + $balance - $amount_invested;
                    
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('investmentChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Invested Amount', 'Profit'],
                                    datasets: [{
                                        data: [$amount_invested, $profit],
                                        backgroundColor: ['#00B2FF', '#BFFF00']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        });
                    </script>";
                }
                elseif (isset($_POST['calculate_goal']) && check_admin_referer('cic_calculator_nonce')) {
                    $present_value = $_POST["present_value"];
                    $years = $_POST["goal_years"];
                    $inflation = $_POST["inflation"];
                    $annual_return = $_POST["goal_annual_return"];

                    list($future_value, $monthly_sip) = calculateGoal($present_value, $years, $inflation, $annual_return);

                    echo "<div class='result'>";
                    echo "<p>Future Value: Rs. " . number_format($future_value, 2) . "</p>";
                    echo "<p>Required Monthly SIP: Rs. " . number_format($monthly_sip, 2) . "</p>";
                    echo "</div>";

                    $total_investment = $monthly_sip * 12 * $years;
                    $total_growth = $future_value - $total_investment;

                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('investmentChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Amount Invested', 'Total Growth'],
                                    datasets: [{
                                        data: [$total_investment, $total_growth],
                                        backgroundColor: ['#00B2FF', '#BFFF00']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        });
                    </script>";
                }
                ?>
            </div>
            <div class="chart-section">
                <div class="chart-container">
                    <canvas id="investmentChart"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #00B2FF;"></div>
                        <span>Invested Amount</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #BFFF00;"></div>
                        <span>Profit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  