<?php 
getUserDetails();
function getUserDetails() 
{
    $dir = "data/";
    $activity_file = "data/userdetails.txt";
    $lines = array();
    if (file_exists($activity_file)) {
        $no_of_entries = 2000;
        
        $fp = fopen($activity_file, "r");
        $activities;
        while (!feof($fp)) {
            $row;
            $line = fgets($fp, 4096);
            $line = str_replace("\n", "", $line);
            if (true) {
                $line = explode(",", $line);
                if (isset($line[0]) && isset($line[1]) && isset($line[2]))
                    $row['browser'] = $line[0] . " " . $line[1] . " " . $line[2];
                if (isset($line[4])) {
                    if (stripos($line[4], "windows") > -1)
                        $row['os'] = "windows";
                    if (stripos($line[4], "linux") > -1)
                        $row['os'] = "linux";
                    if (stripos($line[4], "mac") > -1)
                        $row['os'] = "Mac";
                    else
                        $row['os'] = $line[4];
                }



                if (isset($line[5])) {
                    $user="NA";
                    $action = explode(":",$line[5])[0];
                    if(stripos($line[5],":")>-1){
                        $user = explode(":",$line[5])[1];

                    }
                    $row['action'] = $action;
                    $row['user'] = $user;
                    


                }
                if (isset($line[6])) {
                    $time = explode("GMT", $line[7])[0];
                    $row['time'] = $time;

                }
               


            array_push($lines, $row);
            if (count($lines) > $no_of_entries) {
                break;
            }
        }
                }//end while f!=null
            }//end if activity_file exists       
            $reversed = array_reverse($lines);
            echo json_encode($reversed);
        }

        ?>