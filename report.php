<?php

/**
 * Use this file to output reports required for the SQL Query Design test.
 * An example is provided below. You can use the `asTable` method to pass your query result to,
 * to output it as a styled HTML table.
 */

$database = 'nba2019';
require_once('vendor/autoload.php');
require_once('include/utils.php');

/*
 * Example Query
 * -------------
 * Retrieve all team codes & names
 */
echo '<h1>Example Query</h1>';
$teamSql = "SELECT * FROM team";
$teamResult = query($teamSql);
// dd($teamResult);
echo asTable($teamResult);

/*
 * Report 1
 * --------
 * Produce a query that reports on the best 3pt shooters in the database that are older than 30 years old. Only 
 * retrieve data for players who have shot 3-pointers at greater accuracy than 35%.
 * 
 * Retrieve
 *  - Player name
 *  - Full team name
 *  - Age
 *  - Player number
 *  - Position
 *  - 3-pointers made %
 *  - Number of 3-pointers made 
 *
 * Rank the data by the players with the best % accuracy first.
 */
echo '<h1>Report 1 - Best 3pt Shooters</h1>';
// write your query here

$playerSql = "SELECT roster.name,player_totals.age,team.name as team,roster.number as 'Player Number', roster.pos, 
              (player_totals.3pt / player_totals.3pt_attempted) *100 as '3pt Percentage', player_totals.3pt as '3pt Made'
              FROM roster INNER JOIN player_totals on player_totals.player_id = roster.id
                          INNER JOIN team on roster.team_code = team.code
              WHERE player_totals.age > 30 AND ((player_totals.3pt / player_totals.3pt_attempted) *100) > 35";
$playerResult = query($playerSql);
echo asTable($playerResult);

/*
 * Report 2
 * --------
 * Produce a query that reports on the best 3pt shooting teams. Retrieve all teams in the database and list:
 *  - Team name
 *  - 3-pointer accuracy (as 2 decimal place percentage - e.g. 33.53%) for the team as a whole,
 *  - Total 3-pointers made by the team
 *  - # of contributing players - players that scored at least 1 x 3-pointer
 *  - of attempting player - players that attempted at least 1 x 3-point shot
 *  - total # of 3-point attempts made by players who failed to make a single 3-point shot.
 * 
 * You should be able to retrieve all data in a single query, without subqueries.
 * Put the most accurate 3pt teams first.
 */
echo '<h1>Report 2 - Best 3pt Shooting Teams</h1>';
// write your query here
$teamsSql ="SELECT c.name AS 'Team Name', (SUM(d.3pt) / SUM(d.3pt_attempted) *100) AS '3pt Percentage',
            SUM(d.3pt) AS 'Total 3pt Made',
            count(e.3pt) AS '# of players scored at least 1x3 pointer',
            count(f.3pt_attempted) AS '# of players attempted at least 1x3 pointer', 
            count(g.3pt) AS '# of players who failed to make at least 1x3 pointer'
         
           FROM team c 
           LEFT JOIN roster ON roster.team_code = c.code 
                      LEFT JOIN player_totals d ON d.player_id = roster.id
                      LEFT JOIN player_totals e ON (e.player_id = roster.id) 
                      AND e.3pt >= 1
                               left JOIN player_totals g ON g.player_id = roster.id AND g.3pt = 0
                      left JOIN player_totals f ON f.player_id = roster.id WHERE f.3pt_attempted >=1      
           GROUP BY c.name";

$teamsResult = query($teamsSql);
echo asTable($teamsResult);

?>