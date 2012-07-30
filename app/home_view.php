<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  
  <link rel="stylesheet" type="text/css" href="static4/styles/mini.css">
  <link rel="icon" href="/static4/favicon.ico">
  
  <title>FreeLindenLottery &middot; Free L$ Given Out Daily</title>
  <meta name="description" content="Enter the free lottery daily for the chance to win L$100 or more per day. No surveys, no signups, no obligations!">

  <script type="text/javascript" src="static4/javascript/modernizr.min.js"></script>
</head>
<body>
  <?php
    if(isset($_flash))
    {
      foreach($_flash as $type => $messages)
      {
        ?>
        <div class="flash_<?= $type ?>">
          <ul class="container">
            <?php
              foreach($messages as $message)
              {
                echo "<li>".$message."</li>";
              }
            ?>
          </ul>
        </div>
        <?php
      }
    }
  ?>

  <div class="container">    
    <div id="card">
      <div id="card-header">
        <div id="card-header-logo"></div>
        <div class="given"><span><?= $_total_given ?></span> given out</div>
      </div>
    
      <div id="card-content">
        <div id="card-content-wrap">    
          <div id="home" class="content">
            <img src="<?= getProfileUrl($_results["username"], false) ?>" alt="<?= $_results["display_username"]; ?>'s Profile Image" class="profile-image">
            
            <div class="results">
              <span class="label">Today's Winner</span>
              <?= $_results["display_username"]; ?>
              
              <table>
                <tr>
                  <th>Claimed</th>
                  <th>Winnings</th>
                  <th>Bonus</th>
                  <th>Next Drawing</th>
                </tr>
                
                <tr>
                  <td><?= $_results["claimed"] ? "Yes" : "Not yet" ?></td>
                  <td><?= $_results["prize"] ?></td>
                  <td><?= $_results["bonus"] ?></td>
                  <td><?= $_results["pretty_time"] ?></td>
                </tr>
              </table>
            </div>
            
            <hr>
            
            <?php 
              if($username)
              {
                ?>
                <div class="logged_in">
                  <table>
                    <tr>
                      <th>Welcome back,</th>
                      <th>Your Bonus</th>
                      <th></th>
                      <th></th>
                    </tr>
                    
                    <tr>
                      <td><?= str_replace(".", " ", $username) ?></td>
                      <td><?= $_bonus ?></td>
                      <td>
                        <form method="POST" action="/">
                          <input type="hidden" name="claim">
                          <?php
                            function checkClaimable($claimed, $winner, $username) {
                              if($claimed)
                              {
                                echo " disabled";
                              }
                              else if($winner != $username)
                              {
                                echo " disabled";
                              }
                            }
                          ?>
                          <div class="button<?php checkClaimable($_results["claimed"], $_results["username"], $username) ?>">
                            <input type="submit" value="Collect Prize!"<?php checkClaimable($_results["claimed"], $_results["username"], $username) ?>>
                          </div>
                        </form>
                      </td>
                      <td style="width: 90px;">
                        <form method="POST" action="/" class="logout">
                          <input type="hidden" name="logout">
                          <div class="button">
                            <input type="submit" value="Logout">
                          </div>
                        </form>
                      </td>
                    </tr>
                  </table>
                                         
                  
                </div>
                <?php
              }
              else
              {
                ?>
                <div class="login">
                  <h2>Want to Start Winning?</h2>
                  Using the lottery is absolutely free. All you need to do is type in your Second Life username below and check back every day to see if your name is chosen.
                  
                  <form method="POST" action="/">
                    <input type="text" placeholder="Second Life Username" name="submitUsername">
                    <div class="button">
                      <input type="submit" value="Count me in!">
                    </div>
                  </form>
                </div>
                <?php
              }
            ?>
          </div>
          
          <div id="about" class="content">
            <h1>About</h1>
            <p>
              Every day a lucky winner is chosen to recieve L$100, completely free. If you've typed your username in, you are automagically entered into the lottery every single day. In order to claim your prize, however, you need to come back and check if you have won. Good luck!
            </p>
            
            <h1>Rollovers</h1>
            <p>
              If the winner of the day forgets to check in and doesn't claim their prize, it gets rolled over to the next day. For example, if today's prize is L$100 and it isn't claimed, tomorrow's prize will be L$200 and then L$300 and so on. Make sure you check in daily so you don't miss out.
            </p>
            
            <h1>Bonuses</h1>
            <p>
              Every day you check in to see if you won, you get an extra L$1 in prize money that gets added to your bonus. When you win, you get both the winnings and your bonus. For example, if I win L$200 for the day and my bonus is L$57, I will get L$257 sent to my account.
            </p>
          </div>
          
          <div id="history" class="content">
            <div class="previous scrollable">
              <table>
                <thead>
                   <tr>
                  <th>Previous Winner</th>
                  <th>Claimed</th>
                  <th>Winnings</th>
                  <th>Bonus</th>
                  <th>Date</th>
                </tr>
                </thead>
                
                <?php
                  foreach($_previous_winners as $winner)
                  {
                    ?>
                      <tr>
                        <td><?= $winner["username"] ?></td>
                        <td><?= $winner["claimed"] ?></td>
                        <td><?= $winner["prize"] ?></td>
                        <td><?= $winner["bonus"] ?></td>
                        <td><?= $winner["date"] ?></td>
                      </tr>
                    <?php
                  }
                ?>
              </table>
            </div>
          </div>
          
          <div id="profile" class="content">
            <form method="POST" action="/" class="settings">
              <input type="hidden" name="saveSettings">
              <span class="head">How often do you want an IM reminder?</span>
              <input type="radio" name="notify" id="reminder1" value="1"<? if($_im_settings == 1) echo " checked"; ?>>
                <label for="reminder1">Every day</label>
              <input type="radio" name="notify" id="reminder2" value="2"<? if($_im_settings == 2) echo " checked"; ?>>
                <label for="reminder2">Every other day</label>
              <input type="radio" name="notify" id="reminder0" value="0"<? if($_im_settings == 0) echo " checked"; ?>>
                <label for="reminder0">Never</label>
              
              <div class="button">
                <input type="submit" value="Save IM Settings">
              </div>
            </form>
            
            <span class="head inline">Want to increase your odds of winning?</span>
            Refer a friend and get an extra entry for everyone that joins! All you have to do is give out this personalized link:
            <input type="text" value="http://www.freelindenlottery.com/?r=<?= $userId ?>" class="referral_link" disabled>
            
            <p>
              You have referred
      				<?php 
      					if($referrals == 0)
      					{
      						echo "nobody so far";
      					}
      					else if($referrals == 1)
      					{
      						echo "1 person";
      					}
      					else
      					{
      						echo $referrals." people";
      					}
      				?>
      				and will be entered into the drawing 
      				<?php 
      					if($referrals == 0)
      					{
      						echo "once a day.";
      					}
      					else
      					{
      						echo ($referrals+1)." times every day.";
      					}
      				?>
            </p>
          </div>
          
          <?php
            if(in_array($username, $admins))
            {
              ?>
              <div id="graphs" class="content">
                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                <script type="text/javascript">
                  google.load("visualization", "1", {packages:["corechart"]});
                  google.setOnLoadCallback(drawChart);
                  function drawChart() {
                    var jsonData = '<?= $_checkin_data ?>'
                    var data = new google.visualization.DataTable(jsonData);
                    /*data.addColumn('string', 'Date');
                    data.addColumn('number', 'Check-ins');
                    data.addColumn('number', 'Sign-ups');
                    data.addRows([
                      ['Sep. 12', 15, 2],
                      ['Sep. 13', 12, 3],
                      ['Sep. 25', 14, 3]
                    ]);*/
                    
                    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                      
                    chart.draw(data, {
                      width: 550, 
                      height: 246, 
                      title: 'Lotto Check-ins & Sign-ups (Last 2 Weeks)',
                      hAxis: {
                        slantedText: true,
                      },
                      vAxis: {
                        maxValue: 6
                      },
                      pointSize: 5
                      }
                    );
                  }
                </script>
                <div id="chart_div"></div>
              </div>
              <?php
            }
          ?>

        </div>
      </div>
      <div id="card-footer"></div>
    </div>
    
    <nav>
      <ul class="clearfix">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">History</a></li>
        <?php if($username) { ?><li><a href="#">Profile</a></li><?php } ?>
        <?php if(in_array($username, $admins)) { ?><li><a href="#">Graphs</a></li><?php } ?>
      </ul>
    </nav>
  </div>
  
  <script type="text/javascript" src="static4/javascript/plugins.js"></script>
  <script type="text/javascript" src="static4/javascript/script.js"></script>
</body>
</html>